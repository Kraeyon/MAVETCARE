<?php
// Add StatusHelper at the top of the file
use App\Utils\StatusHelper;

// Database connection and queries
require_once '../config/Database.php';
$db = \Config\Database::getInstance()->getConnection();

// Get today's date in Y-m-d format
$today = date('Y-m-d');
$currentMonth = date('Y-m');

// Get count of today's appointments
$apptStmt = $db->prepare("
    SELECT COUNT(*) FROM appointment 
    WHERE DATE(preferred_date) = ?
");
$apptStmt->execute([$today]);
$todayAppointmentsCount = $apptStmt->fetchColumn();

// Get total clients count
$clientStmt = $db->query("SELECT COUNT(*) FROM client");
$totalClients = $clientStmt->fetchColumn();

// Get total pets count
$petStmt = $db->query("SELECT COUNT(*) FROM pet");
$totalPets = $petStmt->fetchColumn();

// Get total services count
$servicesStmt = $db->query("
    SELECT COUNT(*) FROM service 
    WHERE status = 'ACTIVE' OR status IS NULL
");
$totalServices = $servicesStmt->fetchColumn();

// Get sales data for dashboard
$todaySalesStmt = $db->query("
    SELECT COALESCE(SUM(total_amount), 0) as total, COUNT(*) as count
    FROM sales
    WHERE DATE(sale_date) = CURRENT_DATE
");
$todaySales = $todaySalesStmt->fetch(\PDO::FETCH_ASSOC);

$monthlySalesStmt = $db->query("
    SELECT COALESCE(SUM(total_amount), 0) as total
    FROM sales
    WHERE EXTRACT(YEAR FROM sale_date) = EXTRACT(YEAR FROM CURRENT_DATE)
    AND EXTRACT(MONTH FROM sale_date) = EXTRACT(MONTH FROM CURRENT_DATE)
");
$monthlySales = $monthlySalesStmt->fetch(\PDO::FETCH_ASSOC);

// Handle sorting for appointments
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'preferred_time';
$order = isset($_GET['order']) ? $_GET['order'] : 'ASC';

// Validate sort column (security)
$validSortColumns = [
    'pet_name' => 'p.pet_name',
    'clt_name' => 'CONCAT(c.clt_fname, \' \', c.clt_lname)',
    'preferred_time' => 'a.preferred_time',
    'service_name' => 's.service_name',
    'status' => 'a.status'
];

// Default sort if not valid
$sortColumn = isset($validSortColumns[$sort]) ? $validSortColumns[$sort] : 'a.preferred_time';
$orderDirection = ($order === 'DESC') ? 'DESC' : 'ASC';

// Get today's appointments with details
$todayApptsQuery = "
    SELECT a.appt_code, a.preferred_time, a.status, a.additional_notes,
           c.clt_fname, c.clt_lname, c.clt_contact,
           p.pet_name, p.pet_type, p.pet_breed,
           s.service_name
    FROM appointment a
    JOIN client c ON a.client_code = c.clt_code
    JOIN pet p ON a.pet_code = p.pet_code
    LEFT JOIN service s ON a.service_code = s.service_code
    WHERE DATE(a.preferred_date) = ?
    ORDER BY " . $sortColumn . " " . $orderDirection;

$todayApptsStmt = $db->prepare($todayApptsQuery);
$todayApptsStmt->execute([$today]);
$todayAppointments = $todayApptsStmt->fetchAll(\PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>MavetCare Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
<?php include_once '../app/Views/includes/navbar.php'; ?>

    <div class="d-flex">
    <?php include_once '../app/Views/includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4" style="margin-top: 0;">

        <h4 class="mb-4">Quick Stats</h4>
<div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
    <div class="col">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-check me-2"></i>Today's Appointments</h5>
                <p class="card-text fs-4"><?php echo $todayAppointmentsCount; ?></p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people me-2"></i>Total Clients</h5>
                <p class="card-text fs-4"><?php echo $totalClients; ?></p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-paw me-2"></i>Pets in System</h5>
                <p class="card-text fs-4"><?php echo $totalPets; ?></p>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-gear me-2"></i>Services</h5>
                <p class="card-text fs-4"><?php echo $totalServices; ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Sales Summary -->
<h4 class="mb-4">Sales Summary</h4>
<div class="row row-cols-1 row-cols-md-2 g-4 mb-5">
    <div class="col">
        <div class="card border-0 bg-primary bg-opacity-10 h-100 shadow-sm">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle bg-primary bg-opacity-25 p-3 me-3">
                    <i class="bi bi-currency-dollar text-primary fs-3"></i>
                </div>
                <div>
                    <h5 class="text-primary mb-1">Today's Sales</h5>
                    <h2 class="fw-bold text-dark mb-1">₱<?php echo number_format($todaySales['total'], 2); ?></h2>
                    <div class="badge bg-primary text-white p-2"><?php echo $todaySales['count']; ?> transactions today</div>
                </div>
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card border-0 bg-success bg-opacity-10 h-100 shadow-sm">
            <div class="card-body d-flex align-items-center p-4">
                <div class="rounded-circle bg-success bg-opacity-25 p-3 me-3">
                    <i class="bi bi-graph-up text-success fs-3"></i>
                </div>
                <div>
                    <h5 class="text-success mb-1">Monthly Sales</h5>
                    <h2 class="fw-bold text-dark mb-1">₱<?php echo number_format($monthlySales['total'], 2); ?></h2>
                    <div class="badge bg-success text-white p-2"><?php echo date('F Y'); ?></div>
                </div>
                <div class="ms-auto">
                    <a href="/admin/sales" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

            <!-- Today's Appointments Table -->
<h4 class="mb-3 mt-5">Today's Appointments</h4>
<div class="table-responsive mb-4">
    <table class="table table-bordered table-hover align-middle" id="appointmentsTable">
        <thead class="table-light">
            <tr>
                <th class="sortable" data-sort="pet">Pet Name <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="owner">Owner <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="time">Time <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="service">Service <span class="sort-icon"></span></th>
                <th class="sortable" data-sort="status">Status <span class="sort-icon"></span></th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($todayAppointments) > 0): ?>
                <?php foreach ($todayAppointments as $appt): ?>
                    <tr>
                        <td data-value="<?php echo htmlspecialchars($appt['pet_name']); ?>"><?php echo htmlspecialchars($appt['pet_name']); ?> (<?php echo htmlspecialchars($appt['pet_type']); ?>)</td>
                        <td data-value="<?php echo htmlspecialchars($appt['clt_fname'] . ' ' . $appt['clt_lname']); ?>"><?php echo htmlspecialchars($appt['clt_fname'] . ' ' . $appt['clt_lname']); ?></td>
                        <td data-value="<?php echo $appt['preferred_time']; ?>"><?php echo date('h:i A', strtotime($appt['preferred_time'])); ?></td>
                        <td data-value="<?php echo htmlspecialchars($appt['service_name'] ?? ''); ?>"><?php echo htmlspecialchars($appt['service_name'] ?? 'N/A'); ?></td>
                        <td data-value="<?php echo htmlspecialchars($appt['status']); ?>">
                            <span class="badge <?php echo StatusHelper::getStatusClass($appt['status']); ?>">
                                <?php echo StatusHelper::getDisplayStatus($appt['status']); ?>
                            </span>
                        </td>
                        <td>
                            <div class="btn-group">
                                <?php if (strtolower($appt['status']) === 'pending'): ?>
                                    <button class="btn btn-sm btn-success me-1" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'confirmed')">
                                        <i class="bi bi-check-lg"></i> Approve
                                    </button>
                                <?php elseif (strtolower($appt['status']) === 'confirmed'): ?>
                                    <button class="btn btn-sm btn-info me-1" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'completed')">
                                        <i class="bi bi-check-circle"></i> Complete
                                    </button>
                                <?php endif; ?>
                                
                                <a href="/admin/appointments/edit/<?php echo $appt['appt_code']; ?>" class="btn btn-sm btn-secondary me-1">
                                    <i class="bi bi-pencil"></i> Edit
                                </a>
                                
                                <?php if (strtolower($appt['status']) !== 'cancelled' && strtolower($appt['status']) !== 'completed'): ?>
                                    <button class="btn btn-sm btn-danger" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'cancelled')">
                                        <i class="bi bi-x-lg"></i> Cancel
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">No appointments scheduled for today</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>


<!-- Quick Actions Buttons -->
<div class="d-flex justify-content-start flex-wrap gap-2 mb-5">
    <a href="/admin/appointment" class="btn btn-outline-primary"><i class="bi bi-calendar-plus me-1"></i> Add Appointment</a>
    <a href="/admin/patients" class="btn btn-outline-success"><i class="bi bi-plus-circle me-1"></i> Register New Pet</a>
    <a href="/admin/inventory" class="btn btn-outline-warning"><i class="bi bi-box-seam me-1"></i> Add Inventory</a>
    <a href="/admin/services/add" class="btn btn-outline-info"><i class="bi bi-gear me-1"></i> Add Service</a>
</div>

<!-- Notifications Box -->
<div class="position-fixed bottom-0 end-0 m-4" style="z-index: 1030; width: 350px;">
    <div class="card shadow">
        <div class="card-header bg-danger text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="bi bi-bell-fill me-2"></i>Notifications / Reminders
            </div>
            <?php
            // Count total notifications for badge
            $totalNotifications = 0;
            
            // Get pending appointments
            $pendingStmt = $db->prepare("
                SELECT COUNT(*) FROM appointment 
                WHERE UPPER(status) = 'PENDING' AND DATE(preferred_date) >= ?
            ");
            $pendingStmt->execute([$today]);
            $pendingCount = $pendingStmt->fetchColumn();
            $totalNotifications += $pendingCount;
            
            // Get low stock products (below 10 items)
            $lowStockStmt = $db->query("
                SELECT COUNT(*) FROM product 
                WHERE prod_stock < 10
            ");
            $lowStockCount = $lowStockStmt->fetchColumn();
            $totalNotifications += $lowStockCount;
            
            // Get upcoming appointments
            $nextWeek = date('Y-m-d', strtotime('+7 days'));
            $upcomingStmt = $db->prepare("
                SELECT COUNT(*) FROM appointment 
                WHERE preferred_date BETWEEN ? AND ? AND UPPER(status) = 'CONFIRMED'
            ");
            $upcomingStmt->execute([$today, $nextWeek]);
            $upcomingAppts = $upcomingStmt->fetchColumn();
            $totalNotifications += $upcomingAppts;
            
            // Get recent reviews (last 3 days)
            $recentReviewsDate = date('Y-m-d', strtotime('-3 days'));
            $recentReviewsStmt = $db->prepare("
                SELECT COUNT(*) FROM review 
                WHERE review_date >= ?
            ");
            $recentReviewsStmt->execute([$recentReviewsDate]);
            $recentReviewsCount = $recentReviewsStmt->fetchColumn();
            $totalNotifications += $recentReviewsCount;
            ?>
            
            <?php if ($totalNotifications > 0): ?>
                <span class="badge bg-light text-danger"><?php echo $totalNotifications; ?></span>
            <?php endif; ?>
        </div>
        
        <div class="card-body small p-0">
            <div class="list-group list-group-flush">
                <?php if ($pendingCount > 0): ?>
                    <a href="/admin/appointment?filter=pending" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-clock text-primary me-2"></i>
                            <strong><?php echo $pendingCount; ?></strong> appointment<?php echo $pendingCount != 1 ? 's' : ''; ?> awaiting confirmation
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($lowStockCount > 0): ?>
                    <a href="/admin/inventory?filter=low-stock" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-exclamation-triangle text-danger me-2"></i>
                            <strong><?php echo $lowStockCount; ?></strong> product<?php echo $lowStockCount != 1 ? 's' : ''; ?> low in stock
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($upcomingAppts > 0): ?>
                    <a href="/admin/appointment?filter=upcoming" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-calendar-event text-success me-2"></i>
                            <strong><?php echo $upcomingAppts; ?></strong> upcoming appointment<?php echo $upcomingAppts != 1 ? 's' : ''; ?> next week
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($recentReviewsCount > 0): ?>
                    <a href="/admin/reviews" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                        <div>
                            <i class="bi bi-star text-warning me-2"></i>
                            <strong><?php echo $recentReviewsCount; ?></strong> new review<?php echo $recentReviewsCount != 1 ? 's' : ''; ?> in the last 3 days
                        </div>
                        <i class="bi bi-chevron-right text-muted"></i>
                    </a>
                <?php endif; ?>
                
                <?php if ($totalNotifications == 0): ?>
                    <div class="list-group-item text-center text-muted py-3">
                        <i class="bi bi-check-circle-fill me-2"></i>
                        All caught up! No pending notifications.
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <?php if ($totalNotifications > 0): ?>
        <div class="card-footer bg-light text-center p-2">
            <a href="/admin/appointment" class="text-decoration-none small">
                <i class="bi bi-arrow-right-circle me-1"></i>Manage all appointments
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <style>
    /* Sorting styles */
    .sortable {
        cursor: pointer;
        position: relative;
        user-select: none;
    }
    
    .sortable:hover {
        background-color: #f8f9fa;
    }
    
    .sort-icon::after {
        content: '⇅';
        color: #adb5bd;
        margin-left: 5px;
        font-size: 0.8em;
    }
    
    .sortable.asc .sort-icon::after {
        content: '↑';
        color: #0d6efd;
    }
    
    .sortable.desc .sort-icon::after {
        content: '↓';
        color: #0d6efd;
    }
    </style>
    
    <script>
    function updateAppointmentStatus(apptCode, status) {
        if (confirm("Are you sure you want to mark this appointment as " + status + "?")) {
            // Create form data
            const formData = new FormData();
            formData.append('appt_code', apptCode);
            formData.append('status', status);
            
            // Show loading indicator
            const loadingElement = document.createElement('div');
            loadingElement.className = 'position-fixed top-0 start-0 w-100 h-100 d-flex justify-content-center align-items-center bg-dark bg-opacity-25';
            loadingElement.style.zIndex = '9999';
            loadingElement.innerHTML = '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Loading...</span></div>';
            document.body.appendChild(loadingElement);
            
            // Send fetch request
            fetch('/admin/appointments/update-status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Remove loading indicator
                document.body.removeChild(loadingElement);
                
                if (data.success) {
                    // Show success message
                    const alertElement = document.createElement('div');
                    alertElement.className = 'alert alert-success position-fixed top-0 start-50 translate-middle-x mt-3';
                    alertElement.style.zIndex = '9999';
                    alertElement.innerHTML = 'Appointment status updated successfully';
                    document.body.appendChild(alertElement);
                    
                    // Auto-dismiss alert after 2 seconds
                    setTimeout(() => {
                        document.body.removeChild(alertElement);
                        // Reload page to reflect changes
                        window.location.reload();
                    }, 1500);
                } else {
                    alert("Failed to update appointment status: " + (data.message || "Unknown error"));
                }
            })
            .catch(error => {
                // Remove loading indicator
                if (document.body.contains(loadingElement)) {
                    document.body.removeChild(loadingElement);
                }
                
                console.error('Error:', error);
                alert("An error occurred while updating the appointment status");
            });
        }
    }
    
    // Client-side table sorting
    document.addEventListener('DOMContentLoaded', function() {
        const table = document.getElementById('appointmentsTable');
        const headers = table.querySelectorAll('th.sortable');
        
        // Add click event to sortable headers
        headers.forEach(header => {
            header.addEventListener('click', function() {
                const dataSort = this.getAttribute('data-sort');
                const isAsc = !this.classList.contains('asc');
                
                // Reset all headers
                headers.forEach(h => {
                    h.classList.remove('asc', 'desc');
                });
                
                // Set current sort direction
                this.classList.add(isAsc ? 'asc' : 'desc');
                
                // Get all rows except the header
                const rows = Array.from(table.querySelectorAll('tbody tr'));
                
                // Sort rows
                rows.sort((a, b) => {
                    let cellA, cellB;
                    
                    switch(dataSort) {
                        case 'pet':
                            cellA = a.cells[0].getAttribute('data-value');
                            cellB = b.cells[0].getAttribute('data-value');
                            break;
                        case 'owner':
                            cellA = a.cells[1].getAttribute('data-value');
                            cellB = b.cells[1].getAttribute('data-value');
                            break;
                        case 'time':
                            cellA = a.cells[2].getAttribute('data-value');
                            cellB = b.cells[2].getAttribute('data-value');
                            break;
                        case 'service':
                            cellA = a.cells[3].getAttribute('data-value');
                            cellB = b.cells[3].getAttribute('data-value');
                            break;
                        case 'status':
                            cellA = a.cells[4].getAttribute('data-value');
                            cellB = b.cells[4].getAttribute('data-value');
                            break;
                        default:
                            return 0;
                    }
                    
                    // Check if values are dates or numbers
                    if (dataSort === 'time') {
                        // Time comparison
                        return isAsc 
                            ? new Date('1970/01/01 ' + cellA) - new Date('1970/01/01 ' + cellB) 
                            : new Date('1970/01/01 ' + cellB) - new Date('1970/01/01 ' + cellA);
                    } else {
                        // Default string comparison
                        return isAsc
                            ? cellA.localeCompare(cellB)
                            : cellB.localeCompare(cellA);
                    }
                });
                
                // Remove existing rows
                const tbody = table.querySelector('tbody');
                while (tbody.firstChild) {
                    tbody.removeChild(tbody.firstChild);
                }
                
                // Add sorted rows
                rows.forEach(row => {
                    tbody.appendChild(row);
                });
            });
        });
    });
    </script>
</body>
</html>
