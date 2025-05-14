<?php

$userName = 'Admin';
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 shadow-sm fixed-top" style="background: linear-gradient(180deg, #2c3e50, #34495e); z-index: 1030;">
    <a class="navbar-brand fw-bold text-white" href="#">
        <img src="/assets/images/paw.png" alt="Logo" class="me-2" style="height: 24px;">
        MavetCare
    </a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Admin Dropdown -->
        <div class="dropdown">
            <a class="text-white dropdown-toggle text-decoration-none" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= htmlspecialchars($userName) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
                <li>
                    <form action="/logout" method="POST" style="margin: 0;">
                        <button type="submit" class="dropdown-item text-dark">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Add spacing to compensate for fixed navbar -->
<div style="margin-top: 56px;"></div>
