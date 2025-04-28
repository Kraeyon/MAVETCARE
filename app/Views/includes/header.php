<?php
// This file would be inside the /views directory in MVC structure.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MavetCare - Veterinary Medical Clinic</title>
    <link rel="stylesheet" href="/assets/css/header.css"> <!-- You can define this stylesheet -->
</head>
<body>

<div class="header">
    <div class="logo">
        <img src="paw.png" alt="Logo">
        <span>MavetCare</span>
    </div>

    <div class="nav-links">
        <a href="#" class="active">Home</a>
        <a href="#">About</a>
        <div class="dropdown">
            <a href="#">Services ▼</a> <!-- Added ▼ arrow -->
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
        <a href="#">Products</a>
    </div>
    </div>
    <div class="nav-buttons">
        <a href="#">Book an appointment</a>
        <a href="#">Log in</a>
    </div>
</div>

</body>
</html>