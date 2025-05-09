<?php
include_once '../app/views/includes/header.php';
?>

<?php
use Config\Database;

$pdo = Database::getInstance()->getConnection();

// Get selected category from URL
$category = $_GET['category'] ?? 'all';

if ($category === 'all') {
    $stmt = $pdo->query("SELECT * FROM product");
} else {
    $stmt = $pdo->prepare("SELECT * FROM product WHERE prod_category = :category");
    $stmt->execute(['category' => $category]);
}

$products = $stmt->fetchAll();
?>

<link rel="stylesheet" href="/assets/css/products.css">

<main class="products-container">
  <section class="banner text-center">
    <div class="p-5 position-relative d-flex flex-column align-items-center content">
      <div class="col-md-8 mb-4" data-aos="fade-down" data-aos-delay="100">
        <h1 class="display-5 fw-bold">WELCOME TO OUR PRODUCTS</h1>
        <p class="lead">We offer high-quality, vet-approved products to support your pet's well-being.</p>
      </div>
      
      <div class="d-flex justify-content-center align-items-center flex-wrap mb-4" data-aos="fade-up" data-aos-delay="300">
        <img src="/assets/images/products_header.png" class="img-fluid pets-banner" alt="Various pets including dogs and cats">
      </div>
    </div>
  </section>

  <section class="product-categories">
    <div class="container">
      <div class="row">
        <div class="col-lg-12">
          <div class="category-nav" data-aos="fade-up">
            <a href="?category=shampoo" class="category-btn">Shampoo</a>
            <a href="?category=food-accessories" class="category-btn">Food & Accessories</a>
            <a href="?category=vaccines" class="category-btn">Vaccines</a>
            <a href="?category=injectables" class="category-btn">Injectables</a>
            <a href="?category=anesthetics" class="category-btn">Anesthetics</a>
            <a href="?category=cabinet-stocks" class="category-btn">Cabinet Stocks</a>
          </div>
        </div>
      </div>
    </div>
  </section>

  <section class="featured-products">
    <div class="container my-5">
      <h1 class="text-center mb-4">Featured Products</h1>
      
      <?php
      // Get the selected category from URL parameter
      $category = isset($_GET['category']) ? $_GET['category'] : 'all';
      
      // Display heading based on selected category
      if ($category != 'all') {
        echo '<h3 class="text-center mb-4 category-title">' . ucfirst(str_replace('-', ' ', $category)) . '</h3>';
      }
      ?>
      
      <div class="row">
        <!-- Sample product cards - In a real app, these would be loaded from a database -->
        <?php foreach ($products as $product): ?>
          <div class="col-md-2 mb-1" data-aos="fade-up" data-aos-delay="100" data-category="<?= htmlspecialchars($product['prod_category']) ?>">
              <div class="card h-85 product-card">
                  <img src="/assets/images/products/<?= htmlspecialchars(basename($product['prod_image'])) ?>" class="card-img-top" alt="<?= htmlspecialchars($product['prod_name']) ?>">
                  <div class="card-body">
                      <h5 class="card-title"><?= htmlspecialchars($product['prod_name']) ?></h5>
                      <p class="price">₱<?= number_format($product['prod_price'], 2) ?></p>
                      <a href="#" class="btn btn-primary">View Details</a>
                  </div>
              </div>
          </div>
      <?php endforeach; ?>


      </div>
    </div>
  </section>
</main>

<!-- Add custom scripts before closing body -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  // Initialize AOS animation library
  AOS.init();
  
  // Simple category filter (for demonstration)
  document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const category = urlParams.get('category');
    
    if (category) {
      // Highlight active category button
      document.querySelectorAll('.category-btn').forEach(button => {
        if (button.getAttribute('href').includes(category)) {
          button.classList.add('active');
        }
      });
      
      // Filter products (in a real app, this would likely be handled by PHP)
      document.querySelectorAll('[data-category]').forEach(product => {
        if (product.getAttribute('data-category') !== category && category !== 'all') {
          product.style.display = 'none';
        }
      });
    }
  });
</script>

<?php include_once '../app/views/includes/footer.php'; ?>