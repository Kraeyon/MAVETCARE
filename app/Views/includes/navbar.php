<!-- navbar.php -->
<nav class="navbar navbar-expand-lg navbar-dark bg-primary px-3 shadow-sm" style="background: linear-gradient(180deg, #2c3e50, #34495e);">
    <a class="navbar-brand fw-bold text-white" href="#">
        <i class="bi bi-plus-circle me-2"></i>MavetCare
    </a>

    <div class="ms-auto d-flex align-items-center">
        <!-- Notifications -->
        <div class="position-relative me-3">
            <i class="bi bi-bell-fill fs-5 text-white"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                3
            </span>
        </div>
        
        <!-- Messages -->
        <div class="position-relative me-3">
            <i class="bi bi-chat-dots-fill fs-5 text-white"></i>
            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                5
            </span>
        </div>

        <!-- Admin Dropdown -->
        <div class="dropdown">
            <a class="text-white dropdown-toggle text-decoration-none" href="#" id="adminMenu" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Admin
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="adminMenu">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li><a class="dropdown-item" href="#">Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item" href="#">Logout</a></li>
            </ul>
        </div>
    </div>
</nav>

<!-- Add this to your custom CSS -->
<style>
    /* Navbar background gradient */
    .navbar-dark {
        background: linear-gradient(180deg, #2c3e50, #34495e);
    }

    /* Add shadow to navbar */
    .shadow-sm {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Style the icons in the navbar */
    .navbar i {
        font-size: 1.2rem;  /* Slightly bigger icons */
    }

    /* Make dropdown menu align with the right */
    .dropdown-menu-end {
        right: 0;
    }

    /* Position the badge notifications */
    .position-relative .badge {
        top: -0.75rem;
        right: -0.75rem;
    }

    /* Hover effect for the dropdown and icons */
    .navbar .dropdown-toggle:hover,
    .navbar .position-relative:hover {
        background-color: #444;  /* Darker background on hover */
        border-radius: 5px;
        transition: background-color 0.3s ease;
    }

    /* Make the dropdown items stand out */
    .dropdown-item:hover {
        background-color: #1abc9c;  /* Accent color on hover */
        color: #fff;
    }
</style>
