<?php

$userName = 'Admin';
$notificationCount = 3; 
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 shadow-sm" style="background: linear-gradient(180deg, #2c3e50, #34495e);">
    <a class="navbar-brand fw-bold text-white" href="#">
        <img src="/assets/images/paw.png" alt="Logo" class="me-2" style="height: 24px;">
        MavetCare
    </a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Notifications -->
        <div class="position-relative me-3">
            <i class="bi bi-bell-fill fs-5 text-white"></i>
            <?php if ($notificationCount > 0): ?>
                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                    <?= $notificationCount ?>
                </span>
            <?php endif; ?>
        </div>

        <!-- Admin Dropdown -->
        <div class="dropdown">
            <a class="text-white dropdown-toggle text-decoration-none" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <?= htmlspecialchars($userName) ?>
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
                <li>
                    <form action="/logout" method="POST" style="margin: 0;">
                        <button type="submit" class="dropdown-item">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>
</nav>
