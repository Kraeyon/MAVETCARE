<?php
// Set the current page name dynamically, without the .php extension
$page = basename($_SERVER['PHP_SELF'], ".php");

// Default to 'index' (Dashboard) if no page is set
if ($page == '') {
    $page = 'index';
}

// Update the active check to consider the full URL path or specific page names
$pageUrl = $_SERVER['REQUEST_URI']; // Get the full URL
?>

<!-- sidebar.php -->
<div class="d-flex flex-column bg-light shadow-sm" style="width: 250px; height: 100vh; background: linear-gradient(180deg, #2c3e50, #34495e);">

    <div class="p-3 text-white">
        <h6 class="text-uppercase">Main</h6>

        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'index') ? 'active' : ''; ?>" href="/index">
                    <i class="bi bi-pie-chart-fill me-2"></i>Dashboard
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo (strpos($pageUrl, '/admin/doctor') !== false) ? 'active' : ''; ?>" href="/admin/doctor">
                    <i class="bi bi-people-fill me-2"></i>Doctors
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'patients') ? 'active' : ''; ?>" href="patients.php">
                    <i class="bi bi-person-fill me-2"></i>Patients
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'appointments') ? 'active' : ''; ?>" href="appointments.php">
                    <i class="bi bi-calendar-check-fill me-2"></i>Appointments
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'schedule') ? 'active' : ''; ?>" href="schedule.php">
                    <i class="bi bi-calendar-range-fill me-2"></i>Doctor Schedule
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'inventory') ? 'active' : ''; ?>" href="inventory.php">
                    <i class="bi bi-box-seam me-2"></i>Inventory
                </a>
            </li>

            <li class="nav-item">
                <a class="nav-link <?php echo ($page == 'employees') ? 'active' : ''; ?>" href="employees.php">
                    <i class="bi bi-people me-2"></i>Employees
                </a>
            </li>
        </ul>
    </div>
</div>

<!-- Add this to your custom CSS -->
<style>
    /* Sidebar background gradient */
    .bg-light {
        background: linear-gradient(180deg, #2c3e50, #34495e);
    }

    /* Custom active link style */
    .nav-link.active {
        background-color: #333;  /* Black background */
        color: #fff;  /* White text */
        border-radius: 5px;
        padding-left: 15px;
        transition: all 0.3s ease;
    }

    /* Add smooth hover effect for the links */
    .nav-link:hover {
        background-color: #444;  /* Slightly lighter black on hover */
        color: #fff;
        transition: all 0.3s ease;
    }

    /* Active link hover effect */
    .nav-link.active:hover {
        background-color: #222; /* Slightly darker when active */
    }

    /* Subtle shadow effect on the sidebar */
    .shadow-sm {
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    /* Remove blue outline on focus */
    .nav-link:focus {
        outline: none;
    }

    /* Icon and text alignment */
    .nav-link {
        display: flex;
        align-items: center;
        padding: 12px 20px;
        border-radius: 5px;
        color: #ecf0f1;  /* Light text color */
    }

    .nav-link i {
        font-size: 18px;  /* Icon size */
    }

    .nav-link:hover, .nav-link.active {
        border-left: 4px solid #1abc9c;  /* A nice accent line */
    }

    /* Styling for the sidebar title */
    .p-3.text-white h6 {
        font-weight: bold;
        font-size: 1.1rem;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .d-flex {
            width: 100%;
        }
    }
</style>
