<?php include_once '../app/views/includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Pet Products</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">
  <link rel="stylesheet" href="products.css">
</head>
<body>

<div class="banner text-center">
  <div class="p-5 position-relative d-flex flex-column align-items-center">
    
    <!-- Text content (fade-down) -->
    <div class="col-md-8 mb-2" data-aos="fade-down" data-aos-delay="100">
      <h1 class="display-5 fw-bold">WELCOME TO OUR PRODUCTS</h1>
      <p class="lead">We offer high-quality, vet-approved products to support your pet’s well-being.</p>
    </div>
    
    <!-- Image (fade-up) -->
    <div class="d-flex justify-content-center align-items-center flex-wrap" data-aos="fade-up" data-aos-delay="300">
      <img src="/assets/images/products_header.png" class="animal-img" alt="Pets">
    </div>
    
  </div>
</div>























<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://unpkg.com/aos@2.3.1/dist/aos.js"></script>
<script>
  AOS.init();
</script>
</body>
</html>

<?php include_once '../app/views/includes/footer.php'; ?>

