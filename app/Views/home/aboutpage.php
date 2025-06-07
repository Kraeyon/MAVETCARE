<?php
// MaVetCare Veterinary Website
// Define services offered for dynamic content generation

$features = ["Experienced Vets", "Modern Facilities", "Customer-Centric Service"];


$hours = [
    "monday-friday" => "9:00 AM - 6:00 PM",
    "saturday" => "9:00 AM - 5:00 PM",
    "sunday" => "CLOSED"
];

// Fetch doctor data
require_once '../config/database.php';
use Config\Database;

$pdo = Database::getInstance()->getConnection();

// Function to format schedule for display
function formatSchedule($scheduleDetails) {
    if (!$scheduleDetails || count($scheduleDetails) === 0) {
        return '<span class="text-muted"><i class="bi bi-calendar-x"></i> No schedule set</span>';
    }
    // Parse details into [day => [start, end]]
    $parsed = [];
    foreach ($scheduleDetails as $detail) {
        $parsed[$detail['day_of_week']] = substr($detail['start_time'], 0, 5) . '-' . substr($detail['end_time'], 0, 5);
    }
    // Group days with the same schedule
    $groups = [];
    $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
    $current = null;
    $group = [];
    foreach ($days as $day) {
        $sched = isset($parsed[$day]) ? $parsed[$day] : null;
        if ($sched !== $current) {
            if ($group) {
                $groups[] = [$group, $current];
            }
            $group = [$day];
            $current = $sched;
        } else {
            $group[] = $day;
        }
    }
    if ($group) {
        $groups[] = [$group, $current];
    }
    // Format output
    $out = '<div class="schedule-display">';
    foreach ($groups as [$groupDays, $sched]) {
        if (!$sched) continue;
        $label = count($groupDays) > 1 ? '<b>' . $groupDays[0] . '–' . end($groupDays) . '</b>' : '<b>' . $groupDays[0] . '</b>';
        $out .= '<div class="schedule-block"><i class="bi bi-clock"></i> ' . $label . ': <span class="text-primary">' . $sched . '</span></div>';
    }
    $out .= '</div>';
    return $out;
}

// Fetch all doctors
$stmt = $pdo->query("SELECT * FROM veterinary_staff WHERE LOWER(staff_position) LIKE '%doctor%' AND (status = 'ACTIVE' OR status IS NULL) ORDER BY staff_name ASC");
$doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For each doctor, fetch their schedule as an array of rows
foreach ($doctors as &$doc) {
    $stmt2 = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM staff_schedule WHERE staff_code = ?");
    $stmt2->execute([$doc['staff_code']]);
    $doc['schedule_details'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
unset($doc);

// Fetch testimonials (reviews)
$reviewStmt = $pdo->query("
    SELECT r.*, c.clt_fname, c.clt_lname 
    FROM review r
    JOIN client c ON r.client_code = c.clt_code
    WHERE r.rating >= 4
    ORDER BY r.review_date DESC
    LIMIT 6
");
$testimonials = $reviewStmt->fetchAll(PDO::FETCH_ASSOC);
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
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
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
                             alt="<?php echo htmlspecialchars(ucwords(strtolower($service['service_name']))); ?>"
                             onerror="this.src='/assets/images/services/default.png'">
                        <div class="price-tag position-absolute">₱<?php echo number_format($service['service_fee'], 2); ?></div>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars(ucwords(strtolower($service['service_name']))); ?></h5>
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
                        <h5 class="card-title"><?php echo htmlspecialchars(ucwords(strtolower($product['prod_name']))); ?></h5>
                        <p class="card-text small text-muted">
                            <?php echo htmlspecialchars(ucwords(str_replace('-', ' ', $product['prod_category']))); ?>
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

<!-- Our Doctors Section -->
<section class="py-5">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold">Meet Our Veterinarians</h2>
      <p class="lead text-muted">Our team of experienced professionals is dedicated to providing the best care for your pets</p>
      <div class="divider mx-auto my-3"></div>
    </div>
    
    <?php if (empty($doctors)): ?>
      <div class="text-center">
        <p>Information about our doctors is currently being updated. Please check back later.</p>
      </div>
    <?php else: ?>
      <div class="row g-4 justify-content-center">
        <?php foreach ($doctors as $doc): ?>
          <div class="col-md-6 col-lg-4">
            <div class="doctor-card h-100">
              <div class="card-header d-flex align-items-center">
                <i class="bi bi-person-circle fs-4 me-2"></i>
                <span class="fs-5"><?= htmlspecialchars($doc['staff_name']) ?></span>
              </div>
              <div class="card-body">
                <div class="position-badge mb-3">
                  <span class="badge rounded-pill bg-light text-primary">
                    <i class="bi bi-award me-1"></i><?= htmlspecialchars($doc['staff_position']) ?>
                  </span>
                </div>
                <div class="schedule-container">
                  <h6 class="text-muted mb-2"><i class="bi bi-calendar-week me-2"></i>Available Hours:</h6>
                  <?= formatSchedule($doc['schedule_details']) ?>
                </div>
                <div class="text-center mt-3">
                  <a href="/appointment" class="btn btn-sm btn-outline-primary">Book an Appointment</a>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Testimonials Section -->
<section class="py-5 bg-light">
  <div class="container">
    <div class="text-center mb-5">
      <h2 class="display-5 fw-bold">What Our Clients Say</h2>
      <p class="lead text-muted">Hear from pet owners who trust us with their beloved companions</p>
      <div class="divider mx-auto my-3"></div>
    </div>
    
    <?php if (empty($testimonials)): ?>
      <div class="text-center">
        <p>No testimonials available at the moment. Be the first to leave a review!</p>
        <a href="/reviews" class="btn btn-primary mt-3">Write a Review</a>
      </div>
    <?php else: ?>
      <div class="row g-4 justify-content-center" id="testimonials-container">
        <?php foreach ($testimonials as $testimonial): ?>
          <div class="col-md-6 col-lg-4">
            <div class="testimonial-card h-100">
              <div class="card-body">
                <div class="testimonial-rating mb-3">
                  <?php for ($i = 0; $i < $testimonial['rating']; $i++): ?>
                    <i class="bi bi-star-fill text-warning"></i>
                  <?php endfor; ?>
                  <?php for ($i = $testimonial['rating']; $i < 5; $i++): ?>
                    <i class="bi bi-star text-warning"></i>
                  <?php endfor; ?>
                </div>
                <div class="testimonial-text mb-4">
                  <p class="mb-0">"<?= htmlspecialchars($testimonial['comment']) ?>"</p>
                </div>
                <div class="testimonial-author d-flex align-items-center">
                  <div class="testimonial-avatar me-3">
                    <img src="https://ui-avatars.com/api/?name=<?= urlencode($testimonial['clt_fname'].' '.$testimonial['clt_lname']) ?>&background=random" 
                         class="rounded-circle" width="50" height="50" alt="Client Avatar">
                  </div>
                  <div>
                    <h6 class="mb-0"><?= htmlspecialchars($testimonial['clt_fname'].' '.$testimonial['clt_lname']) ?></h6>
                    <small class="text-muted"><?= date('M d, Y', strtotime($testimonial['review_date'])) ?></small>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <div class="text-center mt-4">
        <a href="/reviews" class="btn btn-outline-primary">See All Reviews</a>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- Why Choose Us Section -->
<section class="py-5">
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