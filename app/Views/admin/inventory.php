<?php
require_once '../config/database.php';
use Config\Database;

$pdo = Database::getInstance()->getConnection();

// Get filter from query string
$filter = isset($_GET['filter']) ? $_GET['filter'] : null;

// Query products based on filter
if ($filter === 'low-stock') {
    $stmt = $pdo->query("
        SELECT p.*
        FROM product p
        WHERE p.prod_stock < 10 AND (p.prod_status = 'ACTIVE' OR p.prod_status IS NULL)
        ORDER BY p.prod_stock ASC
    ");
    $filterTitle = 'Low Stock Products';
} else {
    $stmt = $pdo->query("
        SELECT p.*
        FROM product p
        WHERE p.prod_status = 'ACTIVE' OR p.prod_status IS NULL
        ORDER BY p.prod_name ASC
    ");
    $filterTitle = 'All Products';
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// No longer needed - removed supplier dropdown
// $suppliers = $pdo->query("SELECT * FROM supplier ORDER BY supp_name")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Inventory Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>

<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2><i class="bi bi-box-seam me-2"></i>Inventory Management</h2>
            <div>
                <?php if ($filter !== 'low-stock'): ?>
                    <a href="?filter=low-stock" class="btn btn-warning me-2">
                        <i class="bi bi-exclamation-triangle me-1"></i> Low Stock
                    </a>
                <?php else: ?>
                    <a href="/admin/inventory" class="btn btn-outline-primary me-2">
                        <i class="bi bi-arrow-left me-1"></i> All Products
                    </a>
                <?php endif; ?>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-circle me-1"></i> Add New Product
                </button>
            </div>
        </div>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> Product added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> Product updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['archived'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle me-1"></i> Product archived successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <i class="bi bi-exclamation-triangle-fill me-1"></i>
                <?php 
                $error = $_GET['error'];
                switch($error) {
                    case 'no_product_code':
                        echo 'Error: No product code provided.';
                        break;
                    case 'delete_failed':
                        echo 'Error: Failed to delete the product.';
                        break;
                    case 'archive_failed':
                        echo 'Error: Failed to archive the product.';
                        break;
                    case 'product_not_found':
                        echo 'Error: Product not found.';
                        break;
                    default:
                        echo 'An error occurred.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <div class="card shadow-sm">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="bi bi-box me-2"></i><?php echo $filterTitle; ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th style="width: 70px">Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th style="width: 100px">Stock</th>
                                <th style="width: 120px">Price</th>
                                <th style="width: 120px" class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($product['prod_image']) ?>" 
                                             alt="<?= htmlspecialchars($product['prod_name']) ?>"
                                             class="img-thumbnail rounded"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td class="fw-medium"><?= htmlspecialchars($product['prod_name']) ?></td>
                                    <td>
                                        <span class="badge bg-secondary text-white">
                                            <?= htmlspecialchars($product['prod_category']) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge <?= $product['prod_stock'] < 10 ? 'bg-danger' : 'bg-success' ?> fs-6">
                                            <?= htmlspecialchars($product['prod_stock']) ?>
                                        </span>
                                    </td>
                                    <td class="fw-bold">₱<?= number_format($product['prod_price'], 2) ?></td>
                                    <td>
                                        <div class="d-flex justify-content-center gap-2">
                                            <a href="#" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editProductModal<?php echo $product['prod_code']; ?>" title="Edit">
                                                <i class="bi bi-pencil"></i>
                                            </a>
                                            
                                            <button class="btn btn-sm btn-outline-danger" onclick="confirmArchiveProduct(<?php echo $product['prod_code']; ?>, '<?php echo htmlspecialchars($product['prod_name']); ?>')" title="Archive">
                                                <i class="bi bi-archive"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Edit Product Modal -->
                                <div class="modal fade" id="editProductModal<?= $product['prod_code'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header bg-primary text-white">
                                                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Product</h5>
                                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="/admin/inventory/update" enctype="multipart/form-data">
                                                    <input type="hidden" name="prod_code" value="<?= $product['prod_code'] ?>">
                                                    
                                                    <div class="mb-3">
                                                        <label class="form-label">Product Name</label>
                                                        <input type="text" class="form-control" name="prod_name" 
                                                               value="<?= htmlspecialchars($product['prod_name']) ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Category</label>
                                                        <select class="form-select" name="prod_category" required>
                                                            <option value="shampoo" <?= $product['prod_category'] == 'shampoo' ? 'selected' : '' ?>>Shampoo</option>
                                                            <option value="food-accessories" <?= $product['prod_category'] == 'food-accessories' ? 'selected' : '' ?>>Food & Accessories</option>
                                                            <option value="vaccines" <?= $product['prod_category'] == 'vaccines' ? 'selected' : '' ?>>Vaccines</option>
                                                            <option value="injectables" <?= $product['prod_category'] == 'injectables' ? 'selected' : '' ?>>Injectables</option>
                                                            <option value="anesthetics" <?= $product['prod_category'] == 'anesthetics' ? 'selected' : '' ?>>Anesthetics</option>
                                                            <option value="cabinet-stocks" <?= $product['prod_category'] == 'cabinet-stocks' ? 'selected' : '' ?>>Cabinet Stocks</option>
                                                        </select>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Product Details</label>
                                                        <textarea class="form-control" name="prod_details" rows="3"><?= htmlspecialchars($product['prod_details'] ?? 'No details provided.') ?></textarea>
                                                        <small class="form-text text-muted">Provide additional information about the product</small>
                                                    </div>

                                                    <div class="row mb-3">
                                                        <div class="col-md-6">
                                                            <label class="form-label">Price (₱)</label>
                                                            <input type="number" class="form-control" name="prod_price" 
                                                                value="<?= $product['prod_price'] ?>" step="0.01" required>
                                                        </div>

                                                        <div class="col-md-6">
                                                            <label class="form-label">Stock</label>
                                                            <input type="number" class="form-control" name="prod_stock" 
                                                                value="<?= $product['prod_stock'] ?>" required>
                                                        </div>
                                                    </div>

                                                    <div class="modal-footer px-0 pb-0">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            <i class="bi bi-x-circle me-1"></i>Cancel
                                                        </button>
                                                        <button type="submit" name="update_product" class="btn btn-primary">
                                                            <i class="bi bi-save me-1"></i>Update Product
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Product Modal -->
<div class="modal fade" id="addProductModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/admin/inventory/add" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Product Name</label>
                        <input type="text" class="form-control" name="prod_name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category</label>
                        <select class="form-select" name="prod_category" required>
                            <option value="shampoo">Shampoo</option>
                            <option value="food-accessories">Food & Accessories</option>
                            <option value="vaccines">Vaccines</option>
                            <option value="injectables">Injectables</option>
                            <option value="anesthetics">Anesthetics</option>
                            <option value="cabinet-stocks">Cabinet Stocks</option>
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Details</label>
                        <textarea class="form-control" name="prod_details" rows="3">No details provided.</textarea>
                        <small class="form-text text-muted">Provide additional information about the product</small>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Price (₱)</label>
                            <input type="number" class="form-control" name="prod_price" step="0.01" required>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Stock</label>
                            <input type="number" class="form-control" name="prod_stock" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" name="prod_image" accept="image/*">
                        <div class="form-text">Recommended size: 500x500px. Max size: 2MB.</div>
                    </div>

                    <div class="modal-footer px-0 pb-0">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle me-1"></i>Cancel
                        </button>
                        <button type="submit" name="add_product" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-1"></i>Add Product
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Archive Confirmation Modal -->
<div class="modal fade" id="archiveProductModal" tabindex="-1" aria-labelledby="archiveProductModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="archiveProductModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Archive Product</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to archive the product <strong id="productNameToArchive"></strong>?</p>
                <p>This product will no longer appear in the active inventory list but can be restored from the archived items page.</p>
                <form id="archiveProductForm" action="/admin/inventory/archive" method="POST">
                    <input type="hidden" id="product_id_to_archive" name="product_id">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" onclick="document.getElementById('archiveProductForm').submit()">
                    <i class="bi bi-archive me-1"></i>Archive Product
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Function to confirm archiving a product
    function confirmArchiveProduct(productId, productName) {
        document.getElementById('productNameToArchive').textContent = productName;
        document.getElementById('product_id_to_archive').value = productId;
        
        const modal = new bootstrap.Modal(document.getElementById('archiveProductModal'));
        modal.show();
    }
</script>
</body>
</html>