<?php
// MaVetCare Veterinary Website
// Define services offered for dynamic content generation

$features = ["Experienced Vets", "Modern Facilities", "Customer-Centric Service"];


$hours = [
    "monday-friday" => "9:00 AM - 6:00 PM",
    "saturday" => "9:00 AM - 5:00 PM",
    "sunday" => "CLOSED"
];
?>
<?php include_once '../app/views/includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaVetCare - Professional Veterinary Care</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- AOS Animation CSS -->
    <link href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css" rel="stylesheet">
    <!-- stylesheet -->
    <link rel="stylesheet" href="/assets/css/aboutpage.css">

</head>
<body>

<!-- Hero Section -->
<section class="pt-0 pb-5 bg-light">
  <div class="container">
    <div class="row align-items-center">
      <!-- Left image -->
      <div class="col-md-6 text-center text-md-start" data-aos="fade-right">
        <img src="/assets/images/doctor with pet 1.png" alt="Vet clinic" class="img-fluid">
      </div>

      <!-- Right text -->
      <div class="col-md-6" data-aos="fade-left" data-aos-delay="100">
        <h1 class="display-4">Get to Know MaVetCare</h1>
        <p class="lead">Learn about our story, values, and the team behind your pet's care.</p>
        <a href="/appointment" class="btn btn-primary mt-3" data-aos="zoom-in" data-aos-delay="200">Book an Appointment</a>
      </div>
    </div>
  </div>
</section>


<!-- What Do We Offer Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">What Do We Offer?</h2>
        <div class="row g-4 justify-content-center">
            <?php if (empty($services)): ?>
                <div class="col-12 text-center">
                    <p>Our services are currently being updated. Please check back later.</p>
                </div>
            <?php else: ?>
                <?php 
                // Limit to 3 services
                $display_services = array_slice($services, 0, 3);
                foreach($display_services as $service): 
                ?>
                <div class="col-md-4 col-lg-3">
                    <div class="card h-100 position-relative service-card" style="max-width: 280px; margin: 0 auto;">
                        <img src="<?php echo !empty($service['service_img']) ? htmlspecialchars($service['service_img']) : '/assets/images/services/default.png'; ?>" 
                             class="card-img-top" 
                             alt="<?php echo htmlspecialchars($service['service_name']); ?>"
                             onerror="this.src='/assets/images/services/default.png'">
                        <div class="overlay position-absolute top-50 start-50 translate-middle text-center">
                            <h5 class="text-white"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                        </div>
                        <div class="price-tag position-absolute">₱<?php echo number_format($service['service_fee'], 2); ?></div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($service['service_name']); ?></h5>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/services" class="btn btn-outline-primary">View All Services</a>
        </div>
    </div>
</section>


<!-- Featured Products Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">Featured Products</h2>
        <div class="row g-4 justify-content-center">
            <?php 
            // If no products from database, show fallback sample products
            if (empty($products)) {
                // Define fallback products (only 3)
                $fallback_products = [
                    [
                        'prod_name' => 'Premium Pet Shampoo',
                        'prod_price' => 250.00,
                        'prod_image' => '/assets/images/products/default.png',
                        'prod_category' => 'shampoo'
                    ],
                    [
                        'prod_name' => 'Nutritious Dog Food',
                        'prod_price' => 499.99,
                        'prod_image' => '/assets/images/products/default.png',
                        'prod_category' => 'food-accessories'
                    ],
                    [
                        'prod_name' => 'Pet Vitamins',
                        'prod_price' => 350.00,
                        'prod_image' => '/assets/images/products/default.png',
                        'prod_category' => 'cabinet-stocks'
                    ]
                ];
                
                // Use the fallback products
                $products = $fallback_products;
            } else {
                // Limit to 3 products if there are more
                $products = array_slice($products, 0, 3);
            }
            
            // Display products (either from database or fallback)
            foreach($products as $product): 
            ?>
            <div class="col-md-4 col-lg-3 mb-4">
                <div class="card h-100 position-relative product-card" style="max-width: 280px; margin: 0 auto;">
                    <div class="price-tag position-absolute">₱<?php echo number_format($product['prod_price'], 2); ?></div>
                    <img src="<?php echo !empty($product['prod_image']) ? htmlspecialchars($product['prod_image']) : '/assets/images/products/default.png'; ?>" 
                         class="card-img-top" 
                         alt="<?php echo htmlspecialchars($product['prod_name']); ?>"
                         onerror="this.src='/assets/images/products/default.png'">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($product['prod_name']); ?></h5>
                        <p class="card-text small text-muted">
                            <?php echo htmlspecialchars(ucfirst(str_replace('-', ' ', $product['prod_category']))); ?>
                        </p>
                        <a href="/products" class="btn btn-primary btn-sm mt-2">View Details</a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
        <div class="text-center mt-4">
            <a href="/products" class="btn btn-outline-primary">View All Products</a>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">WHY CHOOSE US?</h2>
        <ul class="list-group list-group-flush">
            <?php foreach($features as $feature): ?>
            <li class="list-group-item d-flex align-items-center">
                <span class="me-2 text-success">✓</span> <?php echo htmlspecialchars($feature); ?>
            </li>
            <?php endforeach; ?>
        </ul>
    </div>
</section>

<!-- Testimonials Section -->
<section class="py-5">
  <div class="container">
    <h2 class="text-center mb-4">CLIENT TESTIMONIALS</h2>
    <div class="row g-4" id="testimonials-container">
      <!-- Testimonials will be dynamically inserted here -->
    </div>
  </div>
</section>


<!-- Contact Hours Section -->
<section class="py-5 bg-light">
    <div class="container">
        <h2 class="text-center mb-4">OUR HOURS</h2>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <table class="table table-bordered text-center">
                    <tbody>
                        <tr>
                            <td>Monday - Friday</td>
                            <td><?php echo htmlspecialchars($hours['monday-friday']); ?></td>
                        </tr>
                        <tr>
                            <td>Saturday</td>
                            <td><?php echo htmlspecialchars($hours['saturday']); ?></td>
                        </tr>
                        <tr>
                            <td>Sunday</td>
                            <td><?php echo htmlspecialchars($hours['sunday']); ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<!-- AOS Animation JS -->
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
  AOS.init();
</script>


</body>
</html>


<?php include_once '../app/views/includes/footer.php'; ?>