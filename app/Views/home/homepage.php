<?php include_once '../app/views/includes/header.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MavetCare - Veterinary Medical Clinic</title>
    <link rel="stylesheet" href="/assets/css/homepage.css"> <!-- You can define this stylesheet -->
</head>
<body>

    <section class="hero">
        <div class="pet-icons">
            <div class="pet-icon" style="top: 10%; left: 5%;">🐾</div>
            <div class="pet-icon" style="top: 20%; left: 15%;">🐾</div>
            <div class="pet-icon" style="top: 70%; left: 8%;">🐾</div>
            <div class="pet-icon" style="top: 40%; right: 15%;">🐾</div>
            <div class="pet-icon" style="top: 80%; right: 10%;">🐾</div>
        </div>

        <div class="hero-content">
            <p class="clinic-name">Welcome to <strong>MavetCare</strong></p>
            <h1>PROVIDING COMPASSIONATE CARE FOR YOUR PETS</h1>
            <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Suspendisse non est at risus cursus sagittis porttitor eu velit.</p>
            <a href="#" class="read-more">Read More</a>
        </div>

        <div class="hero-image">
            <div class="hero-image-container">
                <img src="/assets/images/dogs-with-stethoscope.png" alt="Dog and cat with stethoscope">
            </div>
        </div>
    </section>

    <div class="divider">
        <div class="divider-line"></div>
        <span class="paw-icon">🐾</span>
        <div class="divider-text">Get to know us more</div>
        <span class="paw-icon">🐾</span>
        <div class="divider-line"></div>
    </div>

    <section class="info-section">
    <div class="info-container">
        <div class="info-box">
            <h3>Explore</h3>
            <div class="map-container">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d497.3607081363063!2d123.91190659568983!3d10.319147106849568!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x33a9996cc4806e69%3A0x98c24f76a1744f65!2sFil-Chi%20Animal%20Clinic!5e0!3m2!1sen!2sph!4v1745573059437!5m2!1sen!2sph" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>

        <div class="info-box">
            <h3>Address</h3>
            <p><strong>MavetCare Veterinary Medical Clinic</strong>,<br>
            Juan Luna Ave, Mabolo,<br>
            Cebu City, Philippines.</p>

            <h3 style="margin-top: 20px;">Contact Details</h3>
            <p>📞 233-20-39<br>
            📞 0916-295-8059 <br>
            📞 0956-734-6746 <br>
               ✉️ <a href="mailto:mavetcare@email.com">deliamontanez92@email.com</a>
            </p>
        </div>

        <div class="info-box">
            <h3>Operating Hours</h3>
            <ul class="hours-list">
                <li><strong>Monday to Friday :</strong> 9:00 AM – 6:00 PM</li>
                <li><strong>Saturday :</strong> 9:00 AM – 5:00 PM</li>
                <li><strong>Sunday :</strong> Closed</li>
            </ul>
        </div>
    </div>
</section>

</body>
</html>
