<?php include_once '../app/Views/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Items - MavetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Improved text readability styles */
        body {
            font-size: 16px;
            color: #333;
        }
        
        h2 {
            font-size: 2.2rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        h5.mb-0 {
            font-size: 1.4rem;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .card-header {
            background-color: #f8f9fa;
            border-bottom: 2px solid #e9ecef;
            padding: 1rem;
        }
        
        /* Improved header styling for tab content */
        .tab-pane .card-header {
            background-color: #2c3e50;
            color: white;
            padding: 1rem 1.5rem;
            border-radius: 5px 5px 0 0;
        }
        
        .tab-pane .card-header h5 {
            color: white;
            font-size: 1.5rem;
            font-weight: 500;
            margin: 0;
            letter-spacing: 0.5px;
        }
        
        .table {
            font-size: 1rem;
        }
        
        .table th {
            font-weight: 600;
            color: #2c3e50;
            background-color: #f8f9fa;
        }
        
        .table td {
            vertical-align: middle;
            color: #333;
        }
        
        .text-muted {
            color: #495057 !important;
        }
        
        .nav-tabs .nav-link {
            font-size: 1.1rem;
            font-weight: 500;
            color: #495057;
        }
        
        .nav-tabs .nav-link.active {
            font-weight: 600;
            color: #2c3e50;
            border-bottom: 3px solid #2c3e50;
        }
        
        .empty-state {
            padding: 3rem 0;
        }
        
        .empty-state i {
            font-size: 4rem;
            color: #adb5bd;
            margin-bottom: 1.5rem;
        }
        
        .empty-state p {
            font-size: 1.2rem;
            color: #6c757d;
        }
        
        .modal-title {
            font-size: 1.4rem;
            font-weight: 600;
        }
        
        .modal-body h6 {
            font-size: 1.1rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.75rem;
        }
        
        .modal-body p {
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }
        
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffecb5;
            color: #856404;
            font-weight: 500;
        }
        
        /* Search box styles */
        .search-box {
            position: relative;
            max-width: 300px;
            margin-left: auto;
        }
        
        .search-box .form-control {
            padding-left: 35px;
            border-radius: 20px;
        }
        
        .search-icon {
            position: absolute;
            left: 12px;
            top: 10px;
            color: #6c757d;
        }
        
        .tab-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        @media (max-width: 768px) {
            .search-box {
                margin-top: 10px;
                width: 100%;
                max-width: none;
            }
            
            .tab-header {
                flex-direction: column;
                align-items: flex-start;
            }
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include_once '../app/Views/includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4" style="margin-top: 0;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-archive-fill me-2 text-warning"></i>Archived Items</h2>
            </div>

            <!-- Success/Error Messages -->
            <?php if (isset($_GET['appointment_restored'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> Appointment has been restored successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['product_restored'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> Product has been restored successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['staff_restored'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> Staff member has been restored successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['service_restored'])): ?>
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <strong>Success!</strong> Service has been restored successfully.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_GET['error'])): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <strong>Error!</strong> 
                    <?php 
                        $error = $_GET['error'];
                        if ($error === 'restore_failed') {
                            echo 'Failed to restore the item. Please try again.';
                        } else {
                            echo 'An error occurred.';
                        }
                    ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            <?php endif; ?>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <p class="text-muted fs-5">
                        <i class="bi bi-info-circle me-2"></i>
                        This page shows all archived items. Archived items are not deleted from the system but are no longer active.
                    </p>
                </div>
            </div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-4" id="archiveTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="true">
                        <i class="bi bi-box me-1"></i> Products (<?php echo count($products); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab" aria-controls="staff" aria-selected="false">
                        <i class="bi bi-people me-1"></i> Staff (<?php echo count($staff); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="services-tab" data-bs-toggle="tab" data-bs-target="#services" type="button" role="tab" aria-controls="services" aria-selected="false">
                        <i class="bi bi-gear me-1"></i> Services (<?php echo count($services); ?>)
                    </button>
                </li>
            </ul>
            
            <!-- Tab content -->
            <div class="tab-content" id="archiveTabContent">
                <!-- Products Tab -->
                <div class="tab-pane fade show active" id="products" role="tabpanel" aria-labelledby="products-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="tab-header">
                                <h5 class="mb-0">Archived Products</h5>
                                <div class="search-box">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" id="searchProducts" class="form-control" 
                                        placeholder="Search products..." 
                                        style="padding-left: 35px; border-radius: 20px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($products)): ?>
                                <div class="text-center py-5 empty-state">
                                    <i class="bi bi-box text-muted"></i>
                                    <p class="mt-3">No archived products found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Product Name</th>
                                                <th>Category</th>
                                                <th>Stock</th>
                                                <th>Price</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                                <tr class="product-item"
                                                   data-name="<?= strtolower(htmlspecialchars($product['prod_name'])) ?>"
                                                   data-category="<?= strtolower(htmlspecialchars($product['prod_category'])) ?>"
                                                   data-price="<?= $product['prod_price'] ?>"
                                                   data-code="<?= $product['prod_code'] ?>">
                                                    <td>
                                                        <img src="<?php echo htmlspecialchars($product['prod_image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($product['prod_name']); ?>"
                                                             class="img-thumbnail"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($product['prod_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['prod_category']); ?></td>
                                                    <td><?php echo $product['prod_stock']; ?></td>
                                                    <td>₱<?php echo number_format($product['prod_price'], 2); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#productDetailsModal<?php echo $product['prod_code']; ?>">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>
                                                        
                                                        <form method="POST" action="/admin/inventory/restore" class="d-inline">
                                                            <input type="hidden" name="product_id" value="<?php echo $product['prod_code']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    onclick="return confirm('Are you sure you want to restore this product?')">
                                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal for product details -->
                                                <div class="modal fade" id="productDetailsModal<?php echo $product['prod_code']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Product Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center mb-3">
                                                                    <img src="<?php echo htmlspecialchars($product['prod_image']); ?>" 
                                                                         alt="<?php echo htmlspecialchars($product['prod_name']); ?>"
                                                                         class="img-fluid"
                                                                         style="max-height: 200px;">
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($product['prod_name']); ?></p>
                                                                        <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($product['prod_category']); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Stock:</strong> <?php echo $product['prod_stock']; ?></p>
                                                                        <p class="mb-1"><strong>Price:</strong> ₱<?php echo number_format($product['prod_price'], 2); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="mb-3">
                                                                    <p class="mb-1"><strong>Details:</strong></p>
                                                                    <p><?php echo htmlspecialchars($product['prod_details'] ?? 'No details provided.'); ?></p>
                                                                </div>
                                                                <div class="alert alert-warning">
                                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                                    This product has been archived and is no longer active in the system.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Staff Tab -->
                <div class="tab-pane fade" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="tab-header">
                                <h5 class="mb-0">Archived Staff</h5>
                                <div class="search-box">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" id="searchStaff" class="form-control" 
                                        placeholder="Search staff..." 
                                        style="padding-left: 35px; border-radius: 20px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($staff)): ?>
                                <div class="text-center py-5 empty-state">
                                    <i class="bi bi-people text-muted"></i>
                                    <p class="mt-3">No archived staff members found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staff as $member): ?>
                                                <tr class="staff-item"
                                                   data-name="<?= strtolower(htmlspecialchars($member['staff_name'])) ?>"
                                                   data-position="<?= strtolower(htmlspecialchars($member['staff_position'])) ?>"
                                                   data-contact="<?= strtolower(htmlspecialchars($member['staff_contact'])) ?>"
                                                   data-email="<?= strtolower(htmlspecialchars($member['staff_email_address'])) ?>"
                                                   data-code="<?= $member['staff_code'] ?>">
                                                    <td><?php echo htmlspecialchars($member['staff_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($member['staff_position']); ?></td>
                                                    <td><?php echo htmlspecialchars($member['staff_contact']); ?></td>
                                                    <td><?php echo htmlspecialchars($member['staff_email_address']); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#staffDetailsModal<?php echo $member['staff_code']; ?>">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>
                                                        
                                                        <form method="POST" action="/admin/employees/restore" class="d-inline">
                                                            <input type="hidden" name="staff_code" value="<?php echo $member['staff_code']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    onclick="return confirm('Are you sure you want to restore this staff member?')">
                                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal for staff details -->
                                                <div class="modal fade" id="staffDetailsModal<?php echo $member['staff_code']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Staff Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($member['staff_name']); ?></p>
                                                                        <p class="mb-1"><strong>Position:</strong> <?php echo htmlspecialchars($member['staff_position']); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Contact:</strong> <?php echo htmlspecialchars($member['staff_contact']); ?></p>
                                                                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($member['staff_email_address']); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="alert alert-warning">
                                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                                    This staff member has been archived and is no longer active in the system.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Services Tab -->
                <div class="tab-pane fade" id="services" role="tabpanel" aria-labelledby="services-tab">
                    <div class="card">
                        <div class="card-header">
                            <div class="tab-header">
                                <h5 class="mb-0">Archived Services</h5>
                                <div class="search-box">
                                    <i class="bi bi-search search-icon"></i>
                                    <input type="text" id="searchServices" class="form-control" 
                                        placeholder="Search services..." 
                                        style="padding-left: 35px; border-radius: 20px;">
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <?php if (empty($services)): ?>
                                <div class="text-center py-5 empty-state">
                                    <i class="bi bi-gear text-muted"></i>
                                    <p class="mt-3">No archived services found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Service Name</th>
                                                <th>Description</th>
                                                <th>Fee</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($services as $service): ?>
                                                <tr class="service-item"
                                                   data-name="<?= strtolower(htmlspecialchars($service['service_name'])) ?>"
                                                   data-desc="<?= strtolower(htmlspecialchars($service['service_desc'])) ?>"
                                                   data-fee="<?= $service['service_fee'] ?>"
                                                   data-code="<?= $service['service_code'] ?>">
                                                    <td>
                                                        <img src="<?php echo isset($service['service_img']) ? $service['service_img'] : '/assets/images/services/default.png'; ?>" 
                                                             alt="<?php echo htmlspecialchars($service['service_name']); ?>"
                                                             class="img-thumbnail"
                                                             style="width: 50px; height: 50px; object-fit: cover;"
                                                             onerror="this.src='/assets/images/services/default.png'; this.onerror=null;">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                                    <td><?php echo htmlspecialchars(substr($service['service_desc'], 0, 50) . (strlen($service['service_desc']) > 50 ? '...' : '')); ?></td>
                                                    <td>₱<?php echo number_format($service['service_fee'], 2); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#serviceDetailsModal<?php echo $service['service_code']; ?>">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>
                                                        
                                                        <form method="POST" action="/admin/services/restore" class="d-inline">
                                                            <input type="hidden" name="service_id" value="<?php echo $service['service_code']; ?>">
                                                            <button type="submit" class="btn btn-sm btn-outline-success" 
                                                                    onclick="return confirm('Are you sure you want to restore this service?')">
                                                                <i class="bi bi-arrow-counterclockwise"></i> Restore
                                                            </button>
                                                        </form>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal for service details -->
                                                <div class="modal fade" id="serviceDetailsModal<?php echo $service['service_code']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Service Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center mb-3">
                                                                    <img src="<?php echo isset($service['service_img']) ? $service['service_img'] : '/assets/images/services/default.png'; ?>" 
                                                                         alt="<?php echo htmlspecialchars($service['service_name']); ?>"
                                                                         class="img-fluid"
                                                                         style="max-height: 200px;"
                                                                         onerror="this.src='/assets/images/services/default.png'; this.onerror=null;">
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($service['service_name']); ?></p>
                                                                        <p class="mb-1"><strong>Fee:</strong> ₱<?php echo number_format($service['service_fee'], 2); ?></p>
                                                                    </div>
                                                                    <div class="col-md-12 mt-3">
                                                                        <p class="mb-1"><strong>Description:</strong></p>
                                                                        <p><?php echo htmlspecialchars($service['service_desc']); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="alert alert-warning">
                                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                                    This service has been archived and is no longer active in the system.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to create a "no results" message
        function createNoResultsMessage(containerId, searchValue, itemType) {
            const container = document.getElementById(containerId);
            
            // Remove any existing no results message
            const existingMsg = container.querySelector('.no-results-message');
            if (existingMsg) {
                existingMsg.remove();
            }
            
            // Create new message
            const noResultsDiv = document.createElement('tr');
            noResultsDiv.className = 'no-results-message';
            noResultsDiv.innerHTML = `
                <td colspan="6" class="text-center py-4">
                    <div>
                        <i class="bi bi-search text-muted fs-3"></i>
                        <p class="mt-2 mb-0">No ${itemType} found matching "${searchValue}"</p>
                    </div>
                </td>
            `;
            
            return noResultsDiv;
        }
        
        // Products search functionality
        document.getElementById('searchProducts').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            const productItems = document.querySelectorAll('.product-item');
            let visibleCount = 0;
            
            productItems.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const category = item.getAttribute('data-category') || '';
                const price = item.getAttribute('data-price') || '';
                const code = item.getAttribute('data-code') || '';
                
                if (name.includes(searchValue) || 
                    category.includes(searchValue) || 
                    price.includes(searchValue) || 
                    code.includes(searchValue)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show no results message if needed
            const tbody = document.querySelector('#products table tbody');
            if (visibleCount === 0 && searchValue !== '') {
                const noResultsMsg = createNoResultsMessage('products', searchValue, 'products');
                tbody.appendChild(noResultsMsg);
            } else {
                const existingMsg = tbody.querySelector('.no-results-message');
                if (existingMsg) {
                    existingMsg.remove();
                }
            }
        });
        
        // Staff search functionality
        document.getElementById('searchStaff').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            const staffItems = document.querySelectorAll('.staff-item');
            let visibleCount = 0;
            
            staffItems.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const position = item.getAttribute('data-position') || '';
                const contact = item.getAttribute('data-contact') || '';
                const email = item.getAttribute('data-email') || '';
                const code = item.getAttribute('data-code') || '';
                
                if (name.includes(searchValue) || 
                    position.includes(searchValue) || 
                    contact.includes(searchValue) || 
                    email.includes(searchValue) ||
                    code.includes(searchValue)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show no results message if needed
            const tbody = document.querySelector('#staff table tbody');
            if (visibleCount === 0 && searchValue !== '') {
                const noResultsMsg = createNoResultsMessage('staff', searchValue, 'staff members');
                tbody.appendChild(noResultsMsg);
            } else {
                const existingMsg = tbody.querySelector('.no-results-message');
                if (existingMsg) {
                    existingMsg.remove();
                }
            }
        });
        
        // Services search functionality
        document.getElementById('searchServices').addEventListener('input', function() {
            const searchValue = this.value.toLowerCase().trim();
            const serviceItems = document.querySelectorAll('.service-item');
            let visibleCount = 0;
            
            serviceItems.forEach(item => {
                const name = item.getAttribute('data-name') || '';
                const desc = item.getAttribute('data-desc') || '';
                const fee = item.getAttribute('data-fee') || '';
                const code = item.getAttribute('data-code') || '';
                
                if (name.includes(searchValue) || 
                    desc.includes(searchValue) || 
                    fee.includes(searchValue) || 
                    code.includes(searchValue)) {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Show no results message if needed
            const tbody = document.querySelector('#services table tbody');
            if (visibleCount === 0 && searchValue !== '') {
                const noResultsMsg = createNoResultsMessage('services', searchValue, 'services');
                tbody.appendChild(noResultsMsg);
            } else {
                const existingMsg = tbody.querySelector('.no-results-message');
                if (existingMsg) {
                    existingMsg.remove();
                }
            }
        });
        
        // Clear search when changing tabs
        document.querySelectorAll('button[data-bs-toggle="tab"]').forEach(tab => {
            tab.addEventListener('shown.bs.tab', function() {
                // Clear all search inputs
                document.getElementById('searchProducts').value = '';
                document.getElementById('searchStaff').value = '';
                document.getElementById('searchServices').value = '';
                
                // Show all items in the current tab
                const activeTabId = this.getAttribute('data-bs-target').substring(1);
                const activeTabItems = document.querySelectorAll(`#${activeTabId} tr[class$="-item"]`);
                activeTabItems.forEach(item => {
                    item.style.display = '';
                });
                
                // Remove any no results messages
                const noResultsMsg = document.querySelector(`#${activeTabId} .no-results-message`);
                if (noResultsMsg) {
                    noResultsMsg.remove();
                }
            });
        });
    </script>
</body>
</html> 