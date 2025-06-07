<?php
// footer.php inside /views
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <!-- olive -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MavetCare - Veterinary Medical Clinic</title>

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- Your Custom Footer CSS -->
    <link rel="stylesheet" href="../../assets/css/footer.css">

</head>
<body>

<!-- Footer -->
<footer class="footer">
    <div class="footer-main">
        
        <!-- Quick Links -->
        <div class="footer-section links">
            <h3 class="font-bold mb-4">Quick Links</h3>
            <ul>
                <li>
                    <a href="app/Views/home/index.php" class="blue-link">
                        <span class="icon-circle"><i class="fas fa-home"></i></span>
                        Home
                    </a>
                </li>
                <li>
                    <a href="app/Views/home/aboutpage.php" class="blue-link">
                        <span class="icon-circle"><i class="fas fa-info-circle"></i></span>
                        About
                    </a>
                </li>
                <li>
                    <a href="app/Views/home/services.php" class="blue-link">
                        <span class="icon-circle"><i class="fas fa-stethoscope"></i></span>
                        Services
                    </a>
                </li>
                <li>
                    <a href="app/Views/home/products.php" class="blue-link">
                        <span class="icon-circle"><i class="fas fa-shopping-cart"></i></span>
                        Products
                    </a>
                </li>
            </ul>
        </div>

        <!-- Center Logo and Tagline -->
        <div class="footer-section center">
            <div class="logo-circle">
                <i class="fas fa-paw text-black"></i>
            </div>
            <span class="brand-name">MaVetCare</span>
            <p>Leave your pets in safe hands.</p>
        </div>

        <!-- Contact Section -->
        <div class="footer-section">
            <h3 class="font-bold mb-4">Get in Touch!</h3>

            <div class="contact-email">
                <span class="email-display">deliamontanez92@gmail.com</span>
                <div class="paw-button">
                    <i class="fas fa-paw"></i>
                </div>
            </div>

            <div class="contact-phone">
                <div class="phone-numbers">
                    <div class="phone-item">
                        <span class="icon-circle"><i class="fas fa-phone"></i></span>
                        <a href="tel:2332039" class="phone-link">233 2039</a>
                    </div>
                    <div class="phone-item">
                        <span class="icon-circle"><i class="fas fa-mobile-alt"></i></span>
                        <a href="tel:+639162958059" class="phone-link">+63 916 295 8059</a>
                    </div>
                    <div class="phone-item">
                        <span class="icon-circle"><i class="fas fa-mobile-alt"></i></span>
                        <a href="tel:+639567346746" class="phone-link">+63 956 734 6746</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bottom Text -->
    <div class="footer-bottom">
        All Rights Reserved to <strong>MaVetCare 2025</strong>
    </div>

    <!-- Footer dogs image -->
    <img alt="footer dogs" class="footer-dog-image" src="../../assets/images/footer_lower_right.png" width="330" height="150"/>
    
    <!-- Footer cat image -->
    <img alt="footer cat" class="footer-cat-image" src="../../assets/images/footer_lower_left.png" width="180" height="100"/>
</footer>

</body>
</html>
