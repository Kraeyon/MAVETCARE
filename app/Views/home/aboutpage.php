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
        <p class="lead">Learn about our story, values, and the team behind your pet’s care.</p>
        <a href="/appointment" class="btn btn-primary mt-3" data-aos="zoom-in" data-aos-delay="200">Book an Appointment</a>
      </div>
    </div>
  </div>
</section>


<!-- What Do We Offer Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">What Do We Offer?</h2>
        <div class="row g-4">
            <?php 
            // Updated services array with titles for overlay text
            $services = [
                ["title" => "Vaccinations", "image" => "/assets/images/home_vaccinations.png", "text" => "Vaccinations", "link" => "/vaccination"],
                ["title" => "Deworming", "image" => "/assets/images/home_grooming.png", "text" => "Grooming", "link" => "/grooming"],
                ["title" => "Grooming", "image" => "/assets/images/home_foods.png", "text" => "Foods", "link" => "/products"],
                ["title" => "Surgeries", "image" => "/assets/images/home_medicine.png", "text" => "Medicines", "link" => "/products"]
            ];
            ?>
            <?php foreach($services as $service): ?>
            <div class="col-md-3">
                <a href="<?php echo htmlspecialchars($service['link']); ?>" class="text-decoration-none">
                    <div class="card h-100 position-relative service-card">
                        <img src="<?php echo htmlspecialchars($service['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['title']); ?>">
                        <div class="overlay position-absolute top-50 start-50 translate-middle text-center">
                            <h5 class="text-white"><?php echo htmlspecialchars($service['text']); ?></h5>
                        </div>
                    </div>
                </a>
            </div>
        <?php endforeach; ?>
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