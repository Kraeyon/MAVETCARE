<?php
require_once '../config/database.php';
use Config\Database;

$pdo = Database::getInstance()->getConnection();

// Get filter from query string
$filter = isset($_GET['filter']) ? $_GET['filter'] : null;

// Query products based on filter
if ($filter === 'low-stock') {
    $stmt = $pdo->query("
        SELECT p.*, s.supp_name 
        FROM product p
        LEFT JOIN supplier s ON p.supp_code = s.supp_code
        WHERE p.prod_stock < 10
        ORDER BY p.prod_stock ASC
    ");
    $filterTitle = 'Low Stock Products';
} else {
    $stmt = $pdo->query("
        SELECT p.*, s.supp_name 
        FROM product p
        LEFT JOIN supplier s ON p.supp_code = s.supp_code
        ORDER BY p.prod_name ASC
    ");
    $filterTitle = 'All Products';
}

$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get all suppliers for dropdown
$suppliers = $pdo->query("SELECT * FROM supplier ORDER BY supp_name")->fetchAll(PDO::FETCH_ASSOC);
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
            <h2>Inventory Management</h2>
            <div>
                <a href="?filter=low-stock" class="btn btn-warning me-2">
                    <i class="bi bi-exclamation-triangle"></i> Low Stock
                </a>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addProductModal">
                    <i class="bi bi-plus-circle"></i> Add New Product
                </button>
            </div>
        </div>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">Product added successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Product updated successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Product deleted successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger">
                <?php
                switch ($_GET['error']) {
                    case 'no_product_code':
                        echo 'Error: No product code provided.';
                        break;
                    case 'delete_failed':
                        echo 'Error: Failed to delete the product.';
                        break;
                    case 'invalid_method':
                        echo 'Error: Invalid request method.';
                        break;
                    default:
                        echo 'An error occurred.';
                }
                ?>
            </div>
        <?php endif; ?>

        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><?php echo $filterTitle; ?></h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Image</th>
                                <th>Product Name</th>
                                <th>Category</th>
                                <th>Stock</th>
                                <th>Price</th>
                                <th>Supplier</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $product): ?>
                                <tr>
                                    <td>
                                        <img src="<?= htmlspecialchars($product['prod_image']) ?>" 
                                             alt="<?= htmlspecialchars($product['prod_name']) ?>"
                                             class="img-thumbnail"
                                             style="width: 50px; height: 50px; object-fit: cover;">
                                    </td>
                                    <td><?= htmlspecialchars($product['prod_name']) ?></td>
                                    <td><?= htmlspecialchars($product['prod_category']) ?></td>
                                    <td>
                                        <span class="badge <?= $product['prod_stock'] < 10 ? 'bg-danger' : 'bg-success' ?>">
                                            <?= htmlspecialchars($product['prod_stock']) ?>
                                        </span>
                                    </td>
                                    <td>₱<?= number_format($product['prod_price'], 2) ?></td>
                                    <td><?= htmlspecialchars($product['supp_name'] ?? 'N/A') ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editProductModal<?= $product['prod_code'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="/admin/inventory/delete" class="d-inline">
                                            <input type="hidden" name="prod_code" value="<?= $product['prod_code'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>

                                <!-- Edit Product Modal -->
                                <div class="modal fade" id="editProductModal<?= $product['prod_code'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Product</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                                                        <label class="form-label">Price</label>
                                                        <input type="number" class="form-control" name="prod_price" 
                                                               value="<?= $product['prod_price'] ?>" step="0.01" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Stock</label>
                                                        <input type="number" class="form-control" name="prod_stock" 
                                                               value="<?= $product['prod_stock'] ?>" required>
                                                    </div>

                                                    <div class="mb-3">
                                                        <label class="form-label">Supplier</label>
                                                        <select class="form-select" name="supp_code">
                                                            <option value="">Select Supplier</option>
                                                            <?php foreach ($suppliers as $supplier): ?>
                                                                <option value="<?= $supplier['supp_code'] ?>" 
                                                                        <?= $product['supp_code'] == $supplier['supp_code'] ? 'selected' : '' ?>>
                                                                    <?= htmlspecialchars($supplier['supp_name']) ?>
                                                                </option>
                                                            <?php endforeach; ?>
                                                        </select>
                                                    </div>

                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" name="update_product" class="btn btn-primary">Update Product</button>
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
            <div class="modal-header">
                <h5 class="modal-title">Add New Product</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
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
                        <label class="form-label">Price</label>
                        <input type="number" class="form-control" name="prod_price" step="0.01" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Stock</label>
                        <input type="number" class="form-control" name="prod_stock" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" name="prod_image" accept="image/*">
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <select class="form-select" name="supp_code">
                            <option value="">Select Supplier</option>
                            <?php foreach ($suppliers as $supplier): ?>
                                <option value="<?= $supplier['supp_code'] ?>">
                                    <?= htmlspecialchars($supplier['supp_name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" name="add_product" class="btn btn-primary">Add Product</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>