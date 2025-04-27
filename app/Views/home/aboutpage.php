<?php
// MaVetCare Veterinary Website
// Define services offered for dynamic content generation
$services = [
    [
        "title" => "Vaccinations",
        "image" => "/api/placeholder/240/160"
    ],
    [
        "title" => "Deworming",
        "image" => "/api/placeholder/240/160"
    ],
    [
        "title" => "Grooming",
        "image" => "/api/placeholder/240/160"
    ],
    [
        "title" => "Surgeries",
        "image" => "/api/placeholder/240/160"
    ],
    [
        "title" => "Pet Confinement",
        "image" => "/api/placeholder/240/160"
    ]
];

// Why Choose Us features
$features = [
    "Experienced Vets",
    "Modern Facilities",
    "Customer-Centric Service"
];

// Testimonials
$testimonials = [
    [
        "text" => "MaVetCare gave my dog the best treatment! The staff was caring and professional.",
        "author" => "Maria S.",
        "rating" => 5
    ],
    [
        "text" => "I've been bringing my cats here for years. Dr. Johnson is amazing with animals!",
        "author" => "John D.",
        "rating" => 5
    ],
    [
        "text" => "The grooming services are excellent. My pet always looks and feels great after a visit.",
        "author" => "Sarah T.",
        "rating" => 4
    ]
];

// Hours of operation
$hours = [
    "monday-friday" => "9:00 AM - 6:00 PM",
    "saturday" => "9:00 AM - 5:00 PM",
    "sunday" => "CLOSED"
];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MaVetCare - Professional Veterinary Care</title>
    <link rel="stylesheet" href="/assets/css/aboutpage.css">
</head>
<body>
    <section class="hero">
        <div class="paw-icons">
            <div class="paw-icon" style="top: 10px; left: 20px;">🐾</div>
            <div class="paw-icon" style="bottom: 15px; left: 50px;">🐾</div>
            <div class="paw-icon" style="top: 15px; right: 30px;">🐾</div>
            <div class="paw-icon" style="bottom: 20px; right: 60px;">🐾</div>
        </div>
        
        <div class="hero-image">
            <img src="/api/placeholder/300/200" alt="Vet clinic with happy pets & staff">
        </div>
        
        <div class="hero-content">
            <h1>Welcome to MaVetCare!</h1>
            <p>Providing compassionate care for your pets.</p>
            <a href="#" class="cta-button">Book an Appointment</a>
        </div>
    </section>

    <section class="services">
        <h2 class="section-title">
            <span>OUR </span>
            <span>SERVICES</span>
        </h2>
        
        <div class="service-grid">
            <?php foreach($services as $service): ?>
            <div class="service-item">
                <img src="<?php echo htmlspecialchars($service['image']); ?>" alt="<?php echo htmlspecialchars($service['title']); ?>">
                <div class="service-overlay">
                    <?php echo htmlspecialchars($service['title']); ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </section>

    <section class="features">
        <h2 class="section-title">
            <span>WHY </span>
            <span>CHOOSE US?</span>
        </h2>
        
        <div class="features-container">
            <ul class="features-list">
                <?php foreach($features as $feature): ?>
                <li class="feature-item">
                    <span class="feature-icon">✓</span>
                    <?php echo htmlspecialchars($feature); ?>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="testimonials">
        <h2 class="section-title">
            <span>CLIENT </span>
            <span>TESTIMONIALS</span>
        </h2>
        
        <div class="testimonials-container">
            <div class="testimonial-grid">
                <?php foreach($testimonials as $testimonial): ?>
                <div class="testimonial-card">
                    <div class="testimonial-text">"<?php echo htmlspecialchars($testimonial['text']); ?>"</div>
                    <div class="testimonial-author"><?php echo htmlspecialchars($testimonial['author']); ?></div>
                    <div class="star-rating">
                        <?php 
                        for($i = 0; $i < $testimonial['rating']; $i++) {
                            echo '★';
                        }
                        for($i = $testimonial['rating']; $i < 5; $i++) {
                            echo '☆';
                        }
                        ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="contact">
        <h2 class="section-title contact-title">
            <span>OUR </span>
            <span>HOURS</span>
        </h2>
        
        <div class="contact-container">
            <div class="hours-container">
                <div class="hours-header">OPEN</div>
                
                <div class="hours-row">
                    <div class="day-label">Monday - Friday</div>
                    <div class="time"><?php echo htmlspecialchars($hours['monday-friday']); ?></div>
                </div>
                
                <div class="hours-row">
                    <div class="day-label">Saturday</div>
                    <div class="time"><?php echo htmlspecialchars($hours['saturday']); ?></div>
                </div>
                
                <div class="hours-row">
                    <div class="day-label">Sunday</div>
                    <div class="time"><?php echo htmlspecialchars($hours['sunday']); ?></div>
                </div>
            </div>
        </div>
    </section>

    <?php
    // Appointment booking functionality could be added here
    // if(isset($_POST['book_appointment'])) {
    //     $pet_name = $_POST['pet_name'];
    //     $owner_name = $_POST['owner_name'];
    //     $service_type = $_POST['service_type'];
    //     $appointment_date = $_POST['appointment_date'];
    //     $appointment_time = $_POST['appointment_time'];
    //     $contact_number = $_POST['contact_number'];
    //     
    //     // Process appointment booking
    //     // Store in database
    //     // Send confirmation email
    // }
    ?>
</body>
</html>