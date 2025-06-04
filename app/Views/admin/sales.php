<?php
require_once '../config/database.php';
use Config\Database;

$pdo = Database::getInstance()->getConnection();

// Get filter from query string - default to today's sales
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
$dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : '';

// Query sales based on filter
$params = [];
$whereClause = '';

if ($filter === 'today') {
    $whereClause = "WHERE DATE(s.sale_date) = CURRENT_DATE";
    $filterTitle = "Today's Sales";
} elseif ($filter === 'week') {
    $whereClause = "WHERE s.sale_date >= CURRENT_DATE - INTERVAL '7 days'";
    $filterTitle = "Last 7 Days (Including Today)";
} elseif ($filter === 'month') {
    $whereClause = "WHERE s.sale_date >= CURRENT_DATE - INTERVAL '30 days'";
    $filterTitle = "Last 30 Days (Including Today)";
} elseif ($filter === 'custom' && !empty($dateRange)) {
    // Parse the date range (format: YYYY-MM-DD - YYYY-MM-DD)
    $dates = explode(' - ', $dateRange);
    if (count($dates) === 2) {
        $startDate = $dates[0];
        $endDate = $dates[1];
        // Check if today is included in the range
        $today = date('Y-m-d');
        $includestoday = ($today >= $startDate && $today <= $endDate) ? true : false;
        $whereClause = "WHERE DATE(s.sale_date) BETWEEN ? AND ?";
        $params = [$startDate, $endDate];
        $filterTitle = "Sales from $startDate to $endDate" . ($includestoday ? " (Including Today)" : "");
    }
} else {
    $whereClause = ""; // All sales
    $filterTitle = "All Sales (Including Today)";
}

// Query to get sales with the selected filter
$query = "
    SELECT s.*, COUNT(sd.sale_id) AS item_count
    FROM sales s
    LEFT JOIN sales_details sd ON s.sale_id = sd.sale_id
    $whereClause
    GROUP BY s.sale_id, s.sale_date, s.total_amount
    ORDER BY s.sale_date DESC
";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$sales = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate total sales amount for the filter
$totalQuery = "
    SELECT COALESCE(SUM(total_amount), 0) as total
    FROM sales s
    $whereClause
";

$totalStmt = $pdo->prepare($totalQuery);
$totalStmt->execute($params);
$totalSales = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

// Get products for the add sale form
$productsStmt = $pdo->query("
    SELECT prod_code, prod_name, prod_price, prod_stock
    FROM product
    WHERE (prod_status = 'ACTIVE' OR prod_status IS NULL) AND prod_stock > 0
    ORDER BY prod_name ASC
");
$products = $productsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Sales Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>

<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-cash-register me-2"></i>Sales Management</h2>
            <div>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSaleModal">
                    <i class="bi bi-plus-circle me-1"></i> New Sale
                </button>
            </div>
        </div>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> Sale recorded successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?php 
                $error = $_GET['error'];
                switch($error) {
                    case 'no_items':
                        echo 'Error: Sale must contain at least one item.';
                        break;
                    case 'insufficient_stock':
                        echo 'Error: One or more products have insufficient stock.';
                        break;
                    default:
                        echo 'An error occurred while processing the sale.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <!-- Filter Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-funnel me-2"></i>Filter Sales</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-8">
                        <div class="btn-group mb-3" role="group">
                            <button type="button" class="btn btn<?= $filter !== 'today' ? '-outline' : '' ?>-primary filter-btn" data-filter="today">Today</button>
                            <button type="button" class="btn btn<?= $filter !== 'week' ? '-outline' : '' ?>-primary filter-btn" data-filter="week">Last 7 Days</button>
                            <button type="button" class="btn btn<?= $filter !== 'month' ? '-outline' : '' ?>-primary filter-btn" data-filter="month">Last 30 Days</button>
                            <button type="button" class="btn btn<?= $filter !== 'all' ? '-outline' : '' ?>-primary filter-btn" data-filter="all">All Time</button>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="d-flex">
                            <input type="text" id="date-range" class="form-control me-2" placeholder="Custom date range">
                            <button type="button" id="apply-custom-filter" class="btn btn-primary">Apply</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales Summary Card -->
        <div class="card shadow-sm mb-4">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="bi bi-graph-up me-2"></i>Sales Summary</h5>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="card border-0 bg-primary bg-opacity-10 h-100 shadow-sm">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="rounded-circle bg-primary bg-opacity-25 p-3 me-3">
                                    <i class="bi bi-currency-dollar text-primary fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-primary mb-1">Total Sales</h6>
                                    <h3 class="fw-bold text-dark mb-1" id="total-sales-display">₱<?= number_format($totalSales, 2) ?></h3>
                                    <div class="badge bg-primary text-white" id="filter-title-1"><?= $filterTitle ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card border-0 bg-info bg-opacity-10 h-100 shadow-sm">
                            <div class="card-body d-flex align-items-center p-3">
                                <div class="rounded-circle bg-info bg-opacity-25 p-3 me-3">
                                    <i class="bi bi-receipt text-info fs-3"></i>
                                </div>
                                <div>
                                    <h6 class="text-info mb-1">Number of Transactions</h6>
                                    <h3 class="fw-bold text-dark mb-1" id="transaction-count"><?= count($sales) ?></h3>
                                    <div class="badge bg-info text-white" id="filter-title-2"><?= $filterTitle ?></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sales List Card -->
        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-receipt me-2"></i><span id="filter-title-3"><?= $filterTitle ?></span></h5>
            </div>
            <div class="card-body">
                <div id="sales-loading" class="text-center py-4 d-none">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading sales data...</p>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Sale ID</th>
                                <th>Date & Time</th>
                                <th>Items</th>
                                <th>Total Amount</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="sales-table-body">
                            <?php if (empty($sales)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <div class="text-muted">
                                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                            No sales found for the selected period.
                                        </div>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sales as $sale): ?>
                                    <tr>
                                        <td class="fw-medium">#<?= $sale['sale_id'] ?></td>
                                        <td><?= date('M d, Y h:i A', strtotime($sale['sale_date'])) ?></td>
                                        <td><span class="badge bg-info"><?= $sale['item_count'] ?> items</span></td>
                                        <td class="fw-bold">₱<?= number_format($sale['total_amount'], 2) ?></td>
                                        <td>
                                            <div class="d-flex justify-content-center gap-2">
                                                <button class="btn btn-sm btn-outline-primary" 
                                                        onclick="viewSaleDetails(<?= $sale['sale_id'] ?>)" 
                                                        title="View Details">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                                <button class="btn btn-sm btn-outline-secondary" 
                                                        onclick="printReceipt(<?= $sale['sale_id'] ?>)" 
                                                        title="Print Receipt">
                                                    <i class="bi bi-printer"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Sale Modal -->
<div class="modal fade" id="addSaleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>New Sale</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="saleForm" method="POST" action="/admin/sales/add">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label class="form-label">Product</label>
                            <select id="product-select" class="form-select">
                                <option value="">Select a product</option>
                                <?php foreach ($products as $product): ?>
                                    <option 
                                        value="<?= $product['prod_code'] ?>" 
                                        data-price="<?= $product['prod_price'] ?>"
                                        data-stock="<?= $product['prod_stock'] ?>"
                                        data-name="<?= htmlspecialchars($product['prod_name']) ?>">
                                        <?= htmlspecialchars($product['prod_name']) ?> - ₱<?= number_format($product['prod_price'], 2) ?> (<?= $product['prod_stock'] ?> in stock)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">Qty</label>
                            <input type="number" id="product-qty" class="form-control" value="1" min="1">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label">&nbsp;</label>
                            <button type="button" id="add-item-btn" class="btn btn-success form-control">
                                <i class="bi bi-plus"></i> Add
                            </button>
                        </div>
                    </div>

                    <div class="table-responsive mb-3">
                        <table class="table table-sm table-bordered" id="items-table">
                            <thead class="table-light">
                                <tr>
                                    <th>Product</th>
                                    <th width="100">Price</th>
                                    <th width="80">Quantity</th>
                                    <th width="120">Subtotal</th>
                                    <th width="50">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr id="no-items-row">
                                    <td colspan="5" class="text-center py-3 text-muted">
                                        No items added yet
                                    </td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th id="total-amount">₱0.00</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <div class="text-end">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary" id="save-sale-btn">
                            <i class="bi bi-save me-1"></i> Complete Sale
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Sale Details Modal -->
<div class="modal fade" id="saleDetailsModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-info-circle me-2"></i>Sale Details</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="sale-details-content">
                <!-- Content will be loaded dynamically -->
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                    <p class="mt-2">Loading sale details...</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="print-sale-btn">
                    <i class="bi bi-printer me-1"></i> Print Receipt
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Include JavaScript libraries -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    // Initialize date range picker
    const dateRangePicker = flatpickr("#date-range", {
        mode: "range",
        dateFormat: "Y-m-d",
        defaultDate: <?= !empty($dateRange) ? json_encode($dateRange) : 'null' ?>,
        maxDate: "today"
    });

    // Setup filter buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Check if we should open the New Sale modal
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('action') === 'new') {
            const addSaleModal = new bootstrap.Modal(document.getElementById('addSaleModal'));
            addSaleModal.show();
        }
        
        // Get the current filter from URL or default to 'today'
        const currentFilter = urlParams.get('filter') || 'today';
        const currentDateRange = urlParams.get('date_range') || '';
        
        // Filter buttons click event
        const filterButtons = document.querySelectorAll('.filter-btn');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.getAttribute('data-filter');
                
                // Update active button state immediately
                filterButtons.forEach(btn => {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                });
                this.classList.remove('btn-outline-primary');
                this.classList.add('btn-primary');
                
                // Load data with the selected filter
                loadSalesData(filter);
            });
        });
        
        // Custom date range filter
        document.getElementById('apply-custom-filter').addEventListener('click', function() {
            const dateRange = dateRangePicker.input.value;
            if (!dateRange) {
                alert('Please select a date range');
                return;
            }
            
            // Update button states for custom filter
            filterButtons.forEach(btn => {
                btn.classList.remove('btn-primary');
                btn.classList.add('btn-outline-primary');
            });
            
            loadSalesData('custom', dateRange);
        });
        
        // Function to load sales data via AJAX
        window.loadSalesData = function(filter, dateRange = '') {
            // Show loading indicator
            document.getElementById('sales-loading').classList.remove('d-none');
            document.getElementById('sales-table-body').classList.add('d-none');
            
            // Build the request URL
            let url = `/admin/sales/filter?filter=${filter}`;
            if (filter === 'custom' && dateRange) {
                url += `&date_range=${dateRange}`;
            }
            
            // Add a cache-busting parameter to ensure fresh data
            url += `&_t=${new Date().getTime()}`;
            
            // Make the AJAX request
            fetch(url, { 
                cache: 'no-store',
                headers: {
                    'Pragma': 'no-cache',
                    'Cache-Control': 'no-cache'
                }
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Server responded with status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(data => {
                    if (data.error) {
                        throw new Error(data.error);
                    }
                    
                    // Update the sales table
                    if (data.isEmpty) {
                        document.getElementById('sales-table-body').innerHTML = `
                            <tr>
                                <td colspan="5" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                        No sales found for the selected period.
                                    </div>
                                </td>
                            </tr>
                        `;
                    } else {
                        document.getElementById('sales-table-body').innerHTML = data.sales;
                    }
                    
                    // Update the summary
                    document.getElementById('total-sales-display').textContent = `₱${data.totalSales}`;
                    document.getElementById('transaction-count').textContent = data.count;
                    
                    // Update filter titles
                    document.getElementById('filter-title-1').textContent = data.filterTitle;
                    document.getElementById('filter-title-2').textContent = data.filterTitle;
                    document.getElementById('filter-title-3').textContent = data.filterTitle;
                    
                    // Hide loading indicator
                    document.getElementById('sales-loading').classList.add('d-none');
                    document.getElementById('sales-table-body').classList.remove('d-none');
                    
                    // Update URL without page reload
                    let newUrl = `/admin/sales?filter=${filter}`;
                    if (filter === 'custom' && dateRange) {
                        newUrl += `&date_range=${dateRange}`;
                    }
                    history.pushState({}, '', newUrl);
                })
                .catch(error => {
                    console.error('Error fetching sales data:', error);
                    let errorMessage = error.message || 'Unknown error';
                    
                    document.getElementById('sales-table-body').innerHTML = `
                        <tr>
                            <td colspan="5" class="text-center py-4">
                                <div class="alert alert-danger">
                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                    ${errorMessage}
                                </div>
                            </td>
                        </tr>
                    `;
                    
                    // Reset summary data on error
                    document.getElementById('total-sales-display').textContent = '₱0.00';
                    document.getElementById('transaction-count').textContent = '0';
                    
                    document.getElementById('sales-loading').classList.add('d-none');
                    document.getElementById('sales-table-body').classList.remove('d-none');
                });
        }
        
        // Initialize with current filter on page load
        if (currentFilter) {
            // Set active button
            filterButtons.forEach(btn => {
                const btnFilter = btn.getAttribute('data-filter');
                if (btnFilter === currentFilter) {
                    btn.classList.remove('btn-outline-primary');
                    btn.classList.add('btn-primary');
                } else {
                    btn.classList.remove('btn-primary');
                    btn.classList.add('btn-outline-primary');
                }
            });
        }
        
        // Sale form handling
        const productSelect = document.getElementById('product-select');
        const productQty = document.getElementById('product-qty');
        const addItemBtn = document.getElementById('add-item-btn');
        const itemsTable = document.getElementById('items-table');
        const noItemsRow = document.getElementById('no-items-row');
        const totalAmount = document.getElementById('total-amount');
        const saleForm = document.getElementById('saleForm');
        const saveSaleBtn = document.getElementById('save-sale-btn');
        
        let items = [];
        let total = 0;

        // Add item to the sale
        addItemBtn.addEventListener('click', function() {
            const productCode = productSelect.value;
            if (!productCode) {
                alert('Please select a product');
                return;
            }

            const qty = parseInt(productQty.value, 10);
            if (isNaN(qty) || qty < 1) {
                alert('Please enter a valid quantity');
                return;
            }

            const selectedOption = productSelect.options[productSelect.selectedIndex];
            const productName = selectedOption.getAttribute('data-name');
            const price = parseFloat(selectedOption.getAttribute('data-price'));
            const stock = parseInt(selectedOption.getAttribute('data-stock'), 10);
            
            if (qty > stock) {
                alert(`Only ${stock} units available in stock`);
                return;
            }

            // Check if product already exists in the list
            const existingItem = items.find(item => item.productCode === productCode);
            if (existingItem) {
                const newQty = existingItem.qty + qty;
                if (newQty > stock) {
                    alert(`Cannot add more. Only ${stock} units available in stock`);
                    return;
                }
                existingItem.qty = newQty;
                existingItem.subtotal = newQty * price;
                
                // Update row in the table
                const row = document.getElementById(`item-row-${productCode}`);
                row.querySelector('.item-qty').textContent = newQty;
                row.querySelector('.item-subtotal').textContent = `₱${(newQty * price).toFixed(2)}`;
            } else {
                // Add new item
                const item = {
                    productCode,
                    productName,
                    price,
                    qty,
                    subtotal: price * qty
                };
                items.push(item);

                // Remove no items row if it exists
                if (noItemsRow.parentNode) {
                    noItemsRow.remove();
                }

                // Add row to the table
                const tbody = itemsTable.querySelector('tbody');
                const row = document.createElement('tr');
                row.id = `item-row-${productCode}`;
                row.innerHTML = `
                    <td>${productName}</td>
                    <td>₱${price.toFixed(2)}</td>
                    <td class="item-qty">${qty}</td>
                    <td class="item-subtotal">₱${(price * qty).toFixed(2)}</td>
                    <td>
                        <button type="button" class="btn btn-sm btn-danger remove-item" data-product="${productCode}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                `;
                tbody.appendChild(row);

                // Add remove event listener
                row.querySelector('.remove-item').addEventListener('click', function() {
                    const productCode = this.getAttribute('data-product');
                    removeItem(productCode);
                });
            }

            // Update total
            updateTotal();

            // Reset form
            productSelect.value = '';
            productQty.value = 1;
        });

        // Remove item function
        function removeItem(productCode) {
            items = items.filter(item => item.productCode !== productCode);
            document.getElementById(`item-row-${productCode}`).remove();
            
            // Show no items row if all items removed
            if (items.length === 0) {
                const tbody = itemsTable.querySelector('tbody');
                tbody.appendChild(noItemsRow);
            }
            
            updateTotal();
        }

        // Update total function
        function updateTotal() {
            total = items.reduce((sum, item) => sum + item.subtotal, 0);
            totalAmount.textContent = `₱${total.toFixed(2)}`;
        }

        // Handle form submission
        saleForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            if (items.length === 0) {
                alert('Please add at least one item to the sale');
                return;
            }

            // Disable button to prevent double submission
            saveSaleBtn.disabled = true;
            
            // Add items to the form
            items.forEach((item, index) => {
                const productCodeInput = document.createElement('input');
                productCodeInput.type = 'hidden';
                productCodeInput.name = `products[${index}][product_code]`;
                productCodeInput.value = item.productCode;
                
                const quantityInput = document.createElement('input');
                quantityInput.type = 'hidden';
                quantityInput.name = `products[${index}][quantity]`;
                quantityInput.value = item.qty;
                
                const priceInput = document.createElement('input');
                priceInput.type = 'hidden';
                priceInput.name = `products[${index}][price]`;
                priceInput.value = item.price;
                
                saleForm.appendChild(productCodeInput);
                saleForm.appendChild(quantityInput);
                saleForm.appendChild(priceInput);
            });
            
            // Add total amount
            const totalInput = document.createElement('input');
            totalInput.type = 'hidden';
            totalInput.name = 'total_amount';
            totalInput.value = total;
            saleForm.appendChild(totalInput);
            
            // Submit the form
            saleForm.submit();
        });
    });
    
    // View sale details function
    function viewSaleDetails(saleId) {
        const modal = new bootstrap.Modal(document.getElementById('saleDetailsModal'));
        modal.show();
        
        // Load sale details via AJAX
        fetch(`/admin/sales/details/${saleId}`)
            .then(response => response.json())
            .then(data => {
                document.getElementById('sale-details-content').innerHTML = `
                    <div class="text-center mb-4 border-bottom pb-3">
                        <h6 class="text-muted mb-1">Sale #${data.sale.sale_id}</h6>
                        <h5 class="mb-1">Mabolo Veterinary Clinic</h5>
                        <p class="text-muted small mb-1">Juan Luna Ave, Mabolo, Cebu City, Philippines</p>
                        <p class="text-muted small mb-1">Phone: 233-20-39</p>
                        <p class="text-muted small mb-1">VAT Reg TIN: 649-058-316-00000</p>
                        <p class="small mb-0">Date: ${new Date(data.sale.sale_date).toLocaleString()}</p>
                    </div>
                    <div class="mb-4">
                        <table class="table table-sm">
                            <thead class="table-light">
                                <tr>
                                    <th>Item</th>
                                    <th class="text-end">Qty</th>
                                    <th class="text-end">Price</th>
                                    <th class="text-end">Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${data.items.map(item => `
                                    <tr>
                                        <td>${item.product_name}</td>
                                        <td class="text-end">${item.quantity}</td>
                                        <td class="text-end">₱${parseFloat(item.price).toFixed(2)}</td>
                                        <td class="text-end">₱${(parseFloat(item.quantity) * parseFloat(item.price)).toFixed(2)}</td>
                                    </tr>
                                `).join('')}
                                <tr>
                                    <td colspan="3" class="text-end fw-bold">Total</td>
                                    <td class="text-end fw-bold">₱${parseFloat(data.sale.total_amount).toFixed(2)}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="text-center">
                        <p class="small text-muted mb-1">Thank you for your purchase!</p>
                        <p class="small text-muted mb-0">Come again!</p>
                    </div>
                `;
                
                // Update print button
                document.getElementById('print-sale-btn').onclick = function() {
                    printReceipt(saleId);
                };
            })
            .catch(error => {
                console.error('Error fetching sale details:', error);
                document.getElementById('sale-details-content').innerHTML = `
                    <div class="alert alert-danger">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        Error loading sale details. Please try again.
                    </div>
                `;
            });
    }

    // Print receipt function
    function printReceipt(saleId) {
        window.open(`/admin/sales/print/${saleId}`, '_blank');
    }
</script>
</body>
</html>