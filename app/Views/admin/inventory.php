<?php
require_once '../config/database.php';
use Config\Database;

$pdo = Database::getInstance()->getConnection();

// Handle product addition
if (isset($_POST['add_product'])) {
    $prod_name = $_POST['prod_name'];
    $prod_category = $_POST['prod_category'];
    $prod_price = $_POST['prod_price'];
    $prod_stock = $_POST['prod_stock'];
    $supp_code = $_POST['supp_code'] ?: null;

    // Only process image if one was uploaded
    if (isset($_FILES['prod_image']) && $_FILES['prod_image']['error'] == 0) {
        $image = $_FILES['prod_image'];
        $imageName = time() . '_' . basename($image['name']);
        $uploadDir = '../public/assets/images/products/';
        
        // Create directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        $uploadPath = $uploadDir . $imageName;
        $dbImagePath = '/assets/images/products/' . $imageName;

        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($image['type'], $allowedTypes)) {
            die("Invalid image type. Only JPEG, PNG, and GIF are allowed.");
        }

        if ($image['size'] > 5 * 1024 * 1024) {
            die("Image size too large. Maximum size is 5MB.");
        }

        if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
            die("Image upload failed. Check directory permissions.");
        }
    } else {
        // Default image path if no image is uploaded
        $dbImagePath = '/assets/images/products/default.png';
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO product (prod_name, prod_category, prod_price, prod_stock, prod_image, supp_code) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$prod_name, $prod_category, $prod_price, $prod_stock, $dbImagePath, $supp_code]);
        header("Location: inventory.php?added=1");
        exit;
    } catch (PDOException $e) {
        die("Database error: " . $e->getMessage());
    }
}

$products = $pdo->query("SELECT * FROM product")->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preclinic Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>

<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
        <h2 class="mb-4">Inventory</h2>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">Product added successfully!</div>
        <?php endif; ?>

        <table class="table table-bordered table-striped">
            <thead class="table-dark">
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
                        <td><img src="<?= htmlspecialchars($product['prod_image']) ?>" width="50" height="50" style="object-fit: cover;"></td>
                        <td><?= htmlspecialchars($product['prod_name']) ?></td>
                        <td><?= htmlspecialchars($product['prod_category']) ?></td>
                        <td><?= htmlspecialchars($product['prod_stock']) ?></td>
                        <td>₱<?= number_format($product['prod_price'], 2) ?></td>
                        <td><?= htmlspecialchars($product['supp_code'] ?? 'N/A') ?></td>
                        <td>
                            <a href="inventory.php?edit=<?= $product['prod_id'] ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="inventory.php?delete=<?= $product['prod_id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this product?');">Delete</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Button to trigger modal -->
        <a href="#" class="btn btn-primary mt-3" data-bs-toggle="modal" data-bs-target="#addProductModal">Add New Product</a>

        <!-- Modal for adding a new product -->
        <div class="modal fade" id="addProductModal" tabindex="-1" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Add New Product</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Form with NO action attribute - submits to current URL -->
                        <form method="POST" enctype="multipart/form-data">
                            <!-- Form fields for product details -->
                            <div class="mb-3">
                                <label for="prod_name" class="form-label">Product Name</label>
                                <input type="text" class="form-control" id="prod_name" name="prod_name" required>
                            </div>
                            <div class="mb-3">
                                <label for="prod_category" class="form-label">Category</label>
                                <select class="form-select" id="prod_category" name="prod_category" required>
                                    <option value="shampoo">Shampoo</option>
                                    <option value="food-accessories">Food & Accessories</option>
                                    <option value="vaccines">Vaccines</option>
                                    <option value="injectables">Injectables</option>
                                    <option value="anesthetics">Anesthetics</option>
                                    <option value="cabinet-stocks">Cabinet Stocks</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label for="prod_price" class="form-label">Price</label>
                                <input type="number" class="form-control" id="prod_price" name="prod_price" step="0.01" required>
                            </div>
                            <div class="mb-3">
                                <label for="prod_stock" class="form-label">Stock</label>
                                <input type="number" class="form-control" id="prod_stock" name="prod_stock" required>
                            </div>
                            <div class="mb-3">
                                <label for="prod_image" class="form-label">Product Image</label>
                                <input type="file" class="form-control" id="prod_image" name="prod_image" required>
                            </div>
                            <div class="mb-3">
                                <label for="supp_code" class="form-label">Supplier Code</label>
                                <input type="text" class="form-control" id="supp_code" name="supp_code">
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
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>