<?php
// Start the session to access user data
session_start();
$current_page = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MavetCare - Veterinary Medical Clinic</title>
    <link rel="stylesheet" href="/assets/css/header.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" integrity="sha512-..." crossorigin="anonymous" referrerpolicy="no-referrer" />
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
                <a href="/services" class="<?= (strpos($current_page, '/services') === 0) ? 'active' : '' ?>">Services ▼</a>
                <div class="dropdown-content">
                    <a href="/vaccination">Vaccination</a>
                    <a href="/deworming">Deworming</a>
                    <a href="/antiparasitic">Anti-Parasitic Program</a>
                    <a href="/surgeries">Surgeries</a>
                    <a href="/grooming">Grooming</a>
                    <a href="/treatment">Treatment</a>
                    <a href="/confinement">Confinement</a>
                </div>
            </div>
            <a href="/products" class="<?= ($current_page == '/products') ? 'active' : '' ?>">Products</a>
            <a href="/reviews" class="<?= ($current_page == '/reviews') ? 'active' : '' ?>">Review</a>
        </div>

        <div class="nav-buttons">
            <a href="/appointment" class="<?= ($current_page == '/appointment') ? 'active' : '' ?>">Book an Appointment</a>

            <?php if (isset($_SESSION['user'])): ?>
                <div class="user-dropdown">
                    <div class="user-info" onclick="document.querySelector('.user-menu').classList.toggle('show')">
                        <i class="fa-solid fa-user-circle"></i>
                        <span><?= htmlspecialchars(explode('@', $_SESSION['user']['email'])[0]) ?></span>
                        <i class="fa-solid fa-caret-down"></i>
                    </div>
                    <div class="user-menu">
                        <form method="POST" action="/logout">
                            <button type="submit">Logout</button>
                        </form>
                    </div>
                </div>
            <?php else: ?>
                <a href="/login" class="<?= ($current_page == '/login') ? 'active' : '' ?>">Log in</a>
            <?php endif; ?>
        </div>
    </div>
</div>

</body>
</html>
