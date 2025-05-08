<?php
// Include only the header - it already contains the DOCTYPE, html, head tags
include_once '../app/views/includes/header.php';
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
        <div class="col-md-2 mb-2" data-aos="fade-up" data-aos-delay="100" data-category="shampoo">
          <div class="card h-100 product-card">
            <img src="/assets/images/novapink.png" class="card-img-top" alt="Pet Shampoo">
            <div class="card-body">
              <h5 class="card-title">Premium Pet Shampoo</h5>
              <p class="card-text">Gentle formula for sensitive skin. Perfect for all breeds.</p>
              <p class="price">$14.99</p>
              <a href="#" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>
        
        <div class="col-md-2 mb-2" data-aos="fade-up" data-aos-delay="100" data-category="food-accessories">
          <div class="card h-100 product-card">
            <img src="/assets/images/premium dog food.png" class="card-img-top" alt="Premium Dog Food">
            <div class="card-body">
              <h5 class="card-title">Premium Dog Food</h5>
              <p class="card-text">High-quality nutrition with real ingredients for adult dogs.</p>
              <p class="price">$29.99</p>
              <a href="#" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>
        
        <div class="col-md-2 mb-2" data-aos="fade-up" data-aos-delay="200" data-category="vaccines">
          <div class="card h-100 product-card">
            <img src="/assets/images/vaccine.png" class="card-img-top" alt="Pet Vaccine">
            <div class="card-body">
              <h5 class="card-title">Core Vaccine Package</h5>
              <p class="card-text">Essential vaccines for your pet's health and protection.</p>
              <p class="price">$45.99</p>
              <a href="#" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>

        <div class="col-md-2 mb-2" data-aos="fade-up" data-aos-delay="200" data-category="injectables">
          <div class="card h-100 product-card">
            <img src="/assets/images/injectables.png" class="card-img-top" alt="Pet Injectables">
            <div class="card-body">
              <h5 class="card-title">Injectables Medication</h5>
              <p class="card-text">Essential vaccines for your pet's health and protection.</p>
              <p class="price">$80.99</p>
              <a href="#" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>

        <div class="col-md-2 mb-2" data-aos="fade-up" data-aos-delay="200" data-category="anesthetics">
          <div class="card h-100 product-card">
            <img src="/assets/images/anesthetics.png" class="card-img-top" alt="Pet Anesthetics">
            <div class="card-body">
              <h5 class="card-title">Inhalation Anesthetic</h5>
              <p class="card-text">Essential vaccines for your pet's health and protection.</p>
              <p class="price">$100.99</p>
              <a href="#" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>

        <div class="col-md-2 mb-2" data-aos="fade-up" data-aos-delay="200" data-category="cabinet-stocks">
          <div class="card h-100 product-card">
            <img src="/assets/images/cabinet stocks.png" class="card-img-top" alt="Pet Cabinet Stocks">
            <div class="card-body">
              <h5 class="card-title">Insecticide Soap</h5>
              <p class="card-text">Essential vaccines for your pet's health and protection.</p>
              <p class="price">$108.99</p>
              <a href="#" class="btn btn-primary">View Details</a>
            </div>
          </div>
        </div>
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