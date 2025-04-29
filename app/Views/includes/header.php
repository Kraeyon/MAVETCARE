<?php
// This file would be inside the /views directory in MVC structure.

// Get the current URL path
$current_page = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MavetCare - Veterinary Medical Clinic</title>
    <link rel="stylesheet" href="/assets/css/header.css">
</head>
<body>

<div class="header">
<a href="/" class="logo">
    <img src="/assets/images/paw.png" alt="Logo">
    <span>MavetCare</span>
</a>


    <div style="display: flex; align-items: center; gap: 20px;">
        <div class="nav-links">
            <a href="/" class="<?= ($current_page == '/') ? 'active' : '' ?>">Home</a>
            <a href="/about" class="<?= ($current_page == '/about') ? 'active' : '' ?>">About</a>
            <div class="dropdown">
                <a href="#" class="<?= (strpos($current_page, '/services') === 0) ? 'active' : '' ?>">Services ▼</a>
                <div class="dropdown-content">
                    <a href="#">Vaccination</a>
                    <a href="#">Deworming</a>
                    <a href="#">Anti-Parasitic Program</a>
                    <a href="#">Surgeries</a>
                    <a href="#">Grooming</a>
                    <a href="#">Treatment</a>
                    <a href="#">Confinement</a>
                </div>
            </div>
            <a href="/products" class="<?= ($current_page == '/products') ? 'active' : '' ?>">Products</a>
            <a href="/review" class="<?= ($current_page == '/review') ? 'active' : '' ?>">Review</a>
        </div>    

        <div class="nav-buttons">
            <a href="#">Book an appointment</a>
            <a href="/login" class="<?= ($current_page == '/login') ? 'active' : '' ?>">Log in</a>
        </div>
    </div>
</div>

</body>
</html>
