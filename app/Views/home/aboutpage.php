<?php
// MaVetCare Veterinary Website
// Define services offered for dynamic content generation

$services = [
    ["title" => "Vaccinations", "image" => "/api/placeholder/240/160"],
    ["title" => "Deworming", "image" => "/api/placeholder/240/160"],
    ["title" => "Grooming", "image" => "/api/placeholder/240/160"],
    ["title" => "Surgeries", "image" => "/api/placeholder/240/160"],
    ["title" => "Pet Confinement", "image" => "/api/placeholder/240/160"]
];

$features = ["Experienced Vets", "Modern Facilities", "Customer-Centric Service"];

$testimonials = [
    ["text" => "MaVetCare gave my dog the best treatment! The staff was caring and professional.", "author" => "Maria S.", "rating" => 5],
    ["text" => "I've been bringing my cats here for years. Dr. Johnson is amazing with animals!", "author" => "John D.", "rating" => 5],
    ["text" => "The grooming services are excellent. My pet always looks and feels great after a visit.", "author" => "Sarah T.", "rating" => 4]
];

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
</head>
<body>

<!-- Hero Section -->
<section class="py-5 text-center bg-light">
    <div class="container">
        <img src="/api/placeholder/300/200" alt="Vet clinic" class="img-fluid mb-4">
        <h1 class="display-4">Welcome to MaVetCare!</h1>
        <p class="lead">Providing compassionate care for your pets.</p>
        <a href="#" class="btn btn-primary">Book an Appointment</a>
    </div>
</section>

<!-- Services Section -->
<section class="py-5">
    <div class="container">
        <h2 class="text-center mb-4">OUR SERVICES</h2>
        <div class="row g-4">
            <?php foreach($services as $service): ?>
            <div class="col-md-4">
                <div class="card h-100 text-center">
                    <img src="<?php echo htmlspecialchars($service['image']); ?>" class="card-img-top" alt="<?php echo htmlspecialchars($service['title']); ?>">
                    <div class="card-body">
                        <h5 class="card-title"><?php echo htmlspecialchars($service['title']); ?></h5>
                    </div>
                </div>
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
        <div class="row g-4">
            <?php foreach($testimonials as $testimonial): ?>
            <div class="col-md-4">
                <div class="card h-100 p-3">
                    <blockquote class="blockquote mb-0">
                        <p>"<?php echo htmlspecialchars($testimonial['text']); ?>"</p>
                        <footer class="blockquote-footer"><?php echo htmlspecialchars($testimonial['author']); ?></footer>
                        <div class="mt-2">
                            <?php 
                            for($i = 0; $i < $testimonial['rating']; $i++) echo '★';
                            for($i = $testimonial['rating']; $i < 5; $i++) echo '☆';
                            ?>
                        </div>
                    </blockquote>
                </div>
            </div>
            <?php endforeach; ?>
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
</body>
</html>

<?php include_once '../app/views/includes/footer    .php'; ?>