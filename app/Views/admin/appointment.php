<?php
// Get controller instance from the route handler instead of manual inclusion
use App\Models\AdminAppointmentModel;
use Config\Database;
use App\Utils\StatusHelper;

// Create the model and controller only if they don't exist (to support both direct access and controller rendering)
if (!isset($controller)) {
    $db = Database::getInstance()->getConnection();
    $adminAppointmentModel = new AdminAppointmentModel($db);
    
    // Initialize controller data
    $appointments = [];
    $services = $adminAppointmentModel->getServices();
    $clients = $adminAppointmentModel->getClients();
    $pets = [];
    $message = '';
    $error = '';
    
    // Process form submissions if needed
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Handle client selection to load pets
        if (isset($_POST['selected_client_id']) && !empty($_POST['selected_client_id'])) {
            $pets = $adminAppointmentModel->getPetsByClient($_POST['selected_client_id']);
        }
        
        // Handle appointment add
        if (isset($_POST['add_appointment'])) {
            // Validate form data
            if (empty($_POST['client_code']) || empty($_POST['pet_code']) || empty($_POST['service_code']) || 
                empty($_POST['appt_date']) || empty($_POST['appt_time'])) {
                $error = "Please fill all required fields";
            } else {
                $result = $adminAppointmentModel->addAppointment(
                    $_POST['client_code'],
                    $_POST['pet_code'],
                    $_POST['service_code'],
                    $_POST['appt_date'] . ' ' . $_POST['appt_time'],
                    $_POST['appointment_type'],
                    $_POST['status'],
                    $_POST['additional_notes'] ?? ''
                );
                
                if ($result) {
                    $message = "Appointment added successfully!";
                } else {
                    $error = "Failed to add appointment. Please try again.";
                }
            }
        }
        
        // Handle status update
        if (isset($_POST['update_status'])) {
            $stmt = $db->prepare("UPDATE appointment SET status = ? WHERE appt_code = ?");
            $result = $stmt->execute([$_POST['status'], $_POST['appt_code']]);
            
            if ($result) {
                $message = "Appointment status updated successfully!";
            } else {
                $error = "Failed to update appointment status. Please try again.";
            }
        }
    }
    
    // Handle sorting
    $sort = isset($_GET['sort']) ? $_GET['sort'] : 'appt_datetime';
    $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
    
    // Validate sort column (security)
    $validSortColumns = [
        'appt_code' => 'a.appt_code',
        'client_name' => 'CONCAT(c.clt_fname, \' \', c.clt_lname)',
        'pet_name' => 'p.pet_name',
        'service_name' => 's.service_name',
        'appt_datetime' => 'a.appt_datetime',
        'appointment_type' => 'a.appointment_type',
        'status' => 'a.status'
    ];
    
    // Default sort if not valid
    $sortColumn = isset($validSortColumns[$sort]) ? $validSortColumns[$sort] : 'a.appt_datetime';
    $orderDirection = ($order === 'DESC') ? 'DESC' : 'ASC';
    
    // Add search functionality
    $searchCondition = '';
    $searchParams = [];
    
    if (isset($_GET['search']) && !empty($_GET['search'])) {
        $searchTerm = $_GET['search'];
        $searchCondition = " AND (c.clt_fname LIKE ? OR c.clt_lname LIKE ? OR p.pet_name LIKE ? OR s.service_name LIKE ? OR a.status LIKE ?)";
        $searchParams = ["%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%", "%$searchTerm%"];
    }
    
    // Build query
    $query = "
        SELECT 
            a.*, 
            c.clt_fname, c.clt_lname, c.clt_code as client_code,
            p.pet_name, p.pet_breed, p.pet_code,
            s.service_name, s.service_fee, s.service_code
        FROM 
            appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
        WHERE 1=1" . $searchCondition . "
        ORDER BY " . $sortColumn . " " . $orderDirection;
    
    // Execute query with parameters
    $stmt = $db->prepare($query);
    if (!empty($searchParams)) {
        $stmt->execute($searchParams);
    } else {
        $stmt->execute();
    }
    
    // Load appointments
    $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    // Controller was provided by the route handler
    $appointments = $controller->appointments;
    $services = $controller->services;
    $clients = $controller->clients;
    $pets = $controller->pets;
    $message = $controller->message;
    $error = $controller->error;
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Appointment Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/admin_appointment.css">
    <style>
        .modal {
            display: none;
            position: fixed;
            z-index: 1050;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0,0,0,0.4);
        }
        
        .modal-content {
            background-color: #fefefe;
            margin: 5% auto;
            padding: 20px;
            border: 1px solid #888;
            width: 80%;
            max-width: 600px;
            border-radius: 8px;
        }
        
        .close {
            color: #aaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
            cursor: pointer;
        }
        
        .close:hover,
        .close:focus {
            color: black;
            text-decoration: none;
        }
        
        .form-group {
            margin-bottom: 1rem;
        }
        
        /* Sorting styles */
        .sortable {
            cursor: pointer;
            position: relative;
            user-select: none;
        }
        
        .sortable:hover {
            background-color: #2c3034;
        }
        
        .sort-icon::after {
            content: '⇅';
            color: #adb5bd;
            margin-left: 5px;
            font-size: 0.8em;
        }
        
        .sortable.asc .sort-icon::after {
            content: '↑';
            color: #fff;
        }
        
        .sortable.desc .sort-icon::after {
            content: '↓';
            color: #fff;
        }
        
        /* Calendar styles */
        .calendar-container {
            overflow-x: auto;
        }
        
        .calendar-table {
            table-layout: fixed;
        }
        
        .calendar-table th {
            text-align: center;
            width: 14.28%;
            background-color: #f8f9fa;
        }
        
        .calendar-table td {
            height: 120px;
            vertical-align: top;
            padding: 5px;
        }
        
        .calendar-day {
            font-weight: bold;
            text-align: right;
            margin-bottom: 5px;
            padding: 2px 5px;
            border-radius: 50%;
            display: inline-block;
            min-width: 30px;
        }
        
        .current-day {
            background-color: #0d6efd;
            color: white;
        }
        
        .other-month {
            color: #adb5bd;
            background-color: #f8f9fa;
        }
        
        .appointment-item {
            margin-bottom: 5px;
            padding: 3px 5px;
            border-radius: 3px;
            font-size: 0.8rem;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
            cursor: pointer;
        }
        
        .appointment-pending {
            background-color: #fff3cd;
            border-left: 3px solid #ffc107;
        }
        
        .appointment-confirmed {
            background-color: #d1e7dd;
            border-left: 3px solid #198754;
        }
        
        .appointment-completed {
            background-color: #cff4fc;
            border-left: 3px solid #0dcaf0;
        }
        
        .appointment-cancelled {
            background-color: #f8d7da;
            border-left: 3px solid #dc3545;
            text-decoration: line-through;
        }
        
        /* Improved badge styling */
        .badge {
            font-size: 0.8rem;
            padding: 0.35rem 0.65rem;
            font-weight: 600;
            text-shadow: 0 1px 1px rgba(0,0,0,0.2);
        }
    </style>
</head>
<body>
<?php include_once '../app/Views/includes/navbar.php'; ?>

    <div class="d-flex">
    <?php include_once '../app/Views/includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4" style="margin-top: 0;">
        <h1>Appointments Management</h1>
        
        <?php if($message): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php echo $message; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php echo $error; ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php endif; ?>
        
        <div class="card mb-4">
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h2 class="card-title">Add New Appointment</h2>
                        <button onclick="document.getElementById('addAppointmentModal').style.display='block'" class="btn btn-primary">
                            <i class="bi bi-calendar-plus me-2"></i>Add New Appointment
                        </button>
                        <button onclick="showCalendarView()" class="btn btn-success ms-2">
                            <i class="bi bi-calendar-week me-2"></i>View Confirmed Appointments
                        </button>
                    </div>
                    <div class="col-md-6">
                        <form action="" method="GET" class="d-flex">
                            <div class="input-group">
                                <input type="text" name="search" class="form-control" 
                                    placeholder="Search by name, pet, service, status or ID..." 
                                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>"
                                    style="height: 38px;">
                                <button type="submit" class="btn btn-primary">
                                    <i class="bi bi-search"></i> Search
                                </button>
                                <?php if (isset($_GET['search']) || isset($_GET['id'])): ?>
                                    <a href="/admin/appointment" class="btn btn-outline-secondary">
                                        <i class="bi bi-x-circle"></i> Clear
                                    </a>
                                <?php endif; ?>
                            </div>
                        </form>
                        <?php if ($error): ?>
                            <div class="alert alert-warning mt-2 mb-0 p-2 small">
                                <i class="bi bi-exclamation-triangle-fill me-1"></i> <?php echo $error; ?>
                            </div>
                        <?php elseif ($message && isset($_GET['search'])): ?>
                            <div class="alert alert-success mt-2 mb-0 p-2 small">
                                <i class="bi bi-check-circle-fill me-1"></i> <?php echo $message; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Status Filter Buttons -->
        <div class="card mb-4">
            <div class="card-header bg-light bg-primary text-white" style="background-color: #2c3e50 !important;">
                <h5 class="mb-0" style="color: #ffffff; font-size: 1.4rem; font-weight: 700; text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.7);"><i class="bi bi-funnel me-2" style="color: #4fc3f7;"></i>Filter Appointments</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="/admin/appointment" class="btn btn-outline-dark">
                        <i class="bi bi-card-list me-1"></i>All Appointments
                    </a>
                    <a href="/admin/appointment?filter=pending" class="btn btn-outline-warning" id="btn-filter-pending">
                        <i class="bi bi-clock me-1"></i>Pending
                    </a>
                    <a href="/admin/appointment?filter=confirmed" class="btn btn-outline-success" id="btn-filter-confirmed">
                        <i class="bi bi-check-circle me-1"></i>Confirmed
                    </a>
                    <a href="/admin/appointment?filter=completed" class="btn btn-outline-info" id="btn-filter-completed">
                        <i class="bi bi-trophy me-1"></i>Completed
                    </a>
                    <a href="/admin/appointment?filter=cancelled" class="btn btn-outline-danger" id="btn-filter-cancelled">
                        <i class="bi bi-x-circle me-1"></i>Cancelled
                    </a>
                    <a href="/admin/appointment?filter=upcoming" class="btn btn-outline-primary" id="btn-filter-upcoming">
                        <i class="bi bi-calendar-event me-1"></i>Upcoming (Next 7 Days)
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Calendar View for Scheduled Appointments -->
        <div id="calendarView" class="card mb-4" style="display: none;">
            <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-calendar-week me-2"></i>Confirmed Appointments Calendar</h5>
                <button type="button" class="btn-close btn-close-white" onclick="hideCalendarView()"></button>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">Month</span>
                            <select id="calendarMonth" class="form-select" onchange="updateCalendar()">
                                <?php
                                for ($i = 1; $i <= 12; $i++) {
                                    $month = date('F', mktime(0, 0, 0, $i, 1, date('Y')));
                                    $selected = $i == date('n') ? 'selected' : '';
                                    echo "<option value=\"$i\" $selected>$month</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="input-group">
                            <span class="input-group-text">Year</span>
                            <select id="calendarYear" class="form-select" onchange="updateCalendar()">
                                <?php
                                $currentYear = date('Y');
                                for ($i = $currentYear - 1; $i <= $currentYear + 2; $i++) {
                                    $selected = $i == $currentYear ? 'selected' : '';
                                    echo "<option value=\"$i\" $selected>$i</option>";
                                }
                                ?>
                            </select>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-info mb-3">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    This calendar displays only confirmed and completed appointments. Pending and cancelled appointments are not shown.
                </div>

                <?php if (empty($appointments)): ?>
                <div class="alert alert-warning mb-3">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    No appointments are currently available to display in the calendar. Add some appointments first.
                </div>
                <?php endif; ?>
                
                <div class="calendar-container">
                    <table class="table table-bordered calendar-table">
                        <thead>
                            <tr class="text-center">
                                <th>Sun</th>
                                <th>Mon</th>
                                <th>Tue</th>
                                <th>Wed</th>
                                <th>Thu</th>
                                <th>Fri</th>
                                <th>Sat</th>
                            </tr>
                        </thead>
                        <tbody id="calendarBody">
                            <!-- Calendar will be populated by JavaScript -->
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-body">
                <h2 class="card-title">Appointments List</h2>
                <div class="table-responsive">
                    <table class="table table-striped" id="appointmentsTable">
                        <thead class="table-dark">
                            <tr>
                                <th class="sortable" data-sort="appt_code">ID <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="client_name">Client <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="pet_name">Pet <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="service_name">Service <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="appt_datetime">Date & Time <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="appointment_type">Type <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="status">Status <span class="sort-icon"></span></th>
                                <th>Notes</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($appointments) > 0): ?>
                                <?php foreach($appointments as $appointment): ?>
                                    <tr>
                                        <td data-value="<?php echo $appointment['appt_code']; ?>"><?php echo $appointment['appt_code']; ?></td>
                                        <td data-value="<?php echo htmlspecialchars($appointment['clt_fname'] . ' ' . $appointment['clt_lname']); ?>"><?php echo htmlspecialchars($appointment['clt_fname'] . ' ' . $appointment['clt_lname']); ?></td>
                                        <td data-value="<?php echo htmlspecialchars($appointment['pet_name']); ?>"><?php echo htmlspecialchars($appointment['pet_name'] . ' (' . $appointment['pet_breed'] . ')'); ?></td>
                                        <td data-value="<?php echo htmlspecialchars($appointment['service_name'] ?? ''); ?>"><?php echo htmlspecialchars($appointment['service_name'] . ' - ₱' . number_format($appointment['service_fee'], 2)); ?></td>
                                        <td data-value="<?php echo $appointment['appt_datetime']; ?>"><?php echo date('M d, Y h:i A', strtotime($appointment['appt_datetime'])); ?></td>
                                        <td data-value="<?php echo htmlspecialchars($appointment['appointment_type']); ?>"><?php echo htmlspecialchars($appointment['appointment_type']); ?></td>
                                        <td data-value="<?php echo htmlspecialchars($appointment['status']); ?>">
                                            <span class="badge <?php echo StatusHelper::getStatusClass($appointment['status']); ?>">
                                                <?php echo StatusHelper::getDisplayStatus($appointment['status']); ?>
                                            </span>
                                        </td>
                                        <td data-value="<?php echo htmlspecialchars($appointment['additional_notes'] ?? ''); ?>"><?php echo substr($appointment['additional_notes'], 0, 30) . (strlen($appointment['additional_notes']) > 30 ? '...' : ''); ?></td>
                                        <td>
                                            <!-- Action buttons -->
                                            <div class="btn-group">
                                                <button class="btn btn-sm btn-warning" onclick="openEditModal(<?php echo htmlentities(json_encode($appointment)); ?>)">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                
                                                <!-- Status update buttons -->
                                                <?php if(strtolower($appointment['status']) === 'pending'): ?>
                                                <button class="btn btn-sm btn-success" onclick="updateStatus(<?php echo $appointment['appt_code']; ?>, 'CONFIRMED')">
                                                    <i class="bi bi-check-lg"></i> Approve
                                                </button>
                                                <?php elseif(strtolower($appointment['status']) === 'confirmed'): ?>
                                                <button class="btn btn-sm btn-info" onclick="updateStatus(<?php echo $appointment['appt_code']; ?>, 'COMPLETED')">
                                                    <i class="bi bi-check-circle"></i> Complete
                                                </button>
                                                <?php endif; ?>
                                                
                                                <?php if(strtolower($appointment['status']) !== 'cancelled' && strtolower($appointment['status']) !== 'completed'): ?>
                                                <button class="btn btn-sm btn-danger" onclick="updateStatus(<?php echo $appointment['appt_code']; ?>, 'CANCELLED')">
                                                    <i class="bi bi-x-lg"></i> Cancel
                                                </button>
                                                <?php endif; ?>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="9">No appointments found</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </div>
    
    <!-- Add Appointment Modal -->
    <div id="addAppointmentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('addAppointmentModal').style.display='none'">&times;</span>
            <h2>Add New Appointment</h2>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="client_code">Client:</label>
                    <select name="client_code" id="client_code" class="form-control" required onchange="loadClientPets(this.value)">
                        <option value="">Select Client</option>
                        <?php foreach($clients as $client): ?>
                            <option value="<?php echo $client['clt_code']; ?>">
                                <?php echo $client['clt_fname'] . ' ' . $client['clt_lname']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="pet_code">Pet:</label>
                    <select name="pet_code" id="pet_code" class="form-control" required>
                        <option value="">Select Client First</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="service_code">Service:</label>
                    <select name="service_code" id="service_code" class="form-control" required>
                        <option value="">Select Service</option>
                        <?php foreach($services as $service): ?>
                            <option value="<?php echo $service['service_code']; ?>">
                                <?php echo $service['service_name'] . ' - ₱' . number_format($service['service_fee'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appt_date">Appointment Date:</label>
                    <input type="date" name="appt_date" id="appt_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="appt_time">Appointment Time:</label>
                    <input type="time" name="appt_time" id="appt_time" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="appt_type">Appointment Type:</label>
                    <select name="appointment_type" id="appt_type" class="form-control" required>
                        <option value="WALK-IN">WALK-IN</option>
                        <option value="SERVICE-ON-CALL">SERVICE-ON-CALL</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appt_status">Status:</label>
                    <select name="status" id="appt_status" class="form-control" required>
                        <option value="PENDING">PENDING</option>
                        <option value="CONFIRMED">CONFIRMED</option>
                        <option value="COMPLETED">COMPLETED</option>
                        <option value="CANCELLED">CANCELLED</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="additional_notes">Additional Notes:</label>
                    <textarea name="additional_notes" id="additional_notes" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" name="add_appointment" class="btn btn-primary">Add Appointment</button>
            </form>
        </div>
    </div>
    
    <!-- Edit Appointment Modal -->
    <div id="editAppointmentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('editAppointmentModal').style.display='none'">&times;</span>
            <h2>Edit Appointment</h2>
            <form method="POST" action="">
                <input type="hidden" name="appt_code" id="edit_appt_code">
                
                <div class="form-group">
                    <label for="edit_client_code">Client:</label>
                    <select name="client_code" id="edit_client_code" class="form-control" required onchange="loadClientPetsForEdit(this.value)">
                        <option value="">Select Client</option>
                        <?php foreach($clients as $client): ?>
                            <option value="<?php echo $client['clt_code']; ?>">
                                <?php echo $client['clt_fname'] . ' ' . $client['clt_lname']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_pet_code">Pet:</label>
                    <select name="pet_code" id="edit_pet_code" class="form-control" required>
                        <option value="">Select Client First</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_service_code">Service:</label>
                    <select name="service_code" id="edit_service_code" class="form-control" required>
                        <option value="">Select Service</option>
                        <?php foreach($services as $service): ?>
                            <option value="<?php echo $service['service_code']; ?>">
                                <?php echo $service['service_name'] . ' - ₱' . number_format($service['service_fee'], 2); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_appt_date">Appointment Date:</label>
                    <input type="date" name="appt_date" id="edit_appt_date" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_appt_time">Appointment Time:</label>
                    <input type="time" name="appt_time" id="edit_appt_time" class="form-control" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_appt_type">Appointment Type:</label>
                    <select name="appointment_type" id="edit_appt_type" class="form-control" required>
                        <option value="WALK-IN">WALK-IN</option>
                        <option value="SERVICE-ON-CALL">SERVICE-ON-CALL</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_appt_status">Status:</label>
                    <select name="status" id="edit_appt_status" class="form-control" required>
                        <option value="PENDING">PENDING</option>
                        <option value="CONFIRMED">CONFIRMED</option>
                        <option value="COMPLETED">COMPLETED</option>
                        <option value="CANCELLED">CANCELLED</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_additional_notes">Additional Notes:</label>
                    <textarea name="additional_notes" id="edit_additional_notes" class="form-control" rows="3"></textarea>
                </div>
                
                <button type="submit" name="update_appointment" class="btn btn-primary">Update Appointment</button>
            </form>
        </div>
    </div>
    
    <!-- Status Update Form (hidden) -->
    <form id="statusUpdateForm" method="POST" style="display: none;">
        <input type="hidden" name="appt_code" id="status_appt_code">
        <input type="hidden" name="status" id="status_value">
        <input type="hidden" name="update_status" value="1">
    </form>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Load pets based on selected client
        function loadClientPets(clientCode) {
            if (clientCode === '') {
                document.getElementById('pet_code').innerHTML = '<option value="">Select Client First</option>';
                return;
            }
            
            // Use fetch API to get pets
            fetch('/admin/appointments/get-pets', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'selected_client_id=' + clientCode
            })
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Select Pet</option>';
                data.forEach(pet => {
                    options += `<option value="${pet.pet_code}">${pet.pet_name}</option>`;
                });
                document.getElementById('pet_code').innerHTML = options;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading pets. Please try again.');
            });
        }
        
        // Load pets based on selected client for edit form
        function loadClientPetsForEdit(clientCode) {
            if (clientCode === '') {
                document.getElementById('edit_pet_code').innerHTML = '<option value="">Select Client First</option>';
                return;
            }
            
            // Use fetch API to get pets
            fetch('/admin/appointments/get-pets', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: 'selected_client_id=' + clientCode
            })
            .then(response => response.json())
            .then(data => {
                let options = '<option value="">Select Pet</option>';
                data.forEach(pet => {
                    options += `<option value="${pet.pet_code}">${pet.pet_name}</option>`;
                });
                document.getElementById('edit_pet_code').innerHTML = options;
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading pets. Please try again.');
            });
        }
        
        // Open edit modal and populate form
        function openEditModal(appointment) {
            document.getElementById('edit_appt_code').value = appointment.appt_code;
            document.getElementById('edit_client_code').value = appointment.client_code;
            
            // Load pets for this client
            loadClientPetsForEdit(appointment.client_code);
            
            // Set a timeout to ensure pets are loaded before setting the value
            setTimeout(function() {
                document.getElementById('edit_pet_code').value = appointment.pet_code;
            }, 500);
            
            document.getElementById('edit_service_code').value = appointment.service_code;
            
            // Split the datetime into date and time
            const apptDateTime = new Date(appointment.appt_datetime);
            const dateStr = apptDateTime.toISOString().split('T')[0];
            let timeStr = apptDateTime.toTimeString().split(' ')[0].substring(0, 5);
            
            document.getElementById('edit_appt_date').value = dateStr;
            document.getElementById('edit_appt_time').value = timeStr;
            
            document.getElementById('edit_appt_type').value = appointment.appointment_type;
            document.getElementById('edit_appt_status').value = appointment.status;
            document.getElementById('edit_additional_notes').value = appointment.additional_notes;
            
            document.getElementById('editAppointmentModal').style.display = 'block';
        }
        
        // Update appointment status
        function updateStatus(apptCode, status) {
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
        
        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target.classList.contains('modal')) {
                event.target.style.display = 'none';
            }
        }
        
        // Client-side table sorting
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('appointmentsTable');
            if (!table) return;
            
            const headers = table.querySelectorAll('th.sortable');
            
            // Mark current sort column
            const currentSort = '<?= isset($_GET['sort']) ? $_GET['sort'] : 'appt_datetime' ?>';
            const currentOrder = '<?= isset($_GET['order']) ? $_GET['order'] : 'ASC' ?>';
            
            headers.forEach(header => {
                const dataSort = header.getAttribute('data-sort');
                if (dataSort === currentSort) {
                    header.classList.add(currentOrder.toLowerCase() === 'asc' ? 'asc' : 'desc');
                }
                
                // Add click event to sortable headers
                header.addEventListener('click', function() {
                    const dataSort = this.getAttribute('data-sort');
                    let newOrder = 'ASC';
                    
                    // Toggle order if already sorted by this column
                    if (dataSort === currentSort) {
                        newOrder = currentOrder.toUpperCase() === 'ASC' ? 'DESC' : 'ASC';
                    }
                    
                    // Redirect with sort parameters
                    const currentUrl = new URL(window.location.href);
                    const searchParams = currentUrl.searchParams;
                    
                    // Preserve search term if exists
                    const searchTerm = searchParams.get('search');
                    
                    // Create new URL with sort parameters
                    let url = '?sort=' + dataSort + '&order=' + newOrder;
                    if (searchTerm) {
                        url += '&search=' + encodeURIComponent(searchTerm);
                    }
                    
                    window.location.href = url;
                });
            });
            
            // Initialize calendar
            try {
                // Safely check if appointmentsData exists and has items before initializing
                if (typeof appointmentsData !== 'undefined' && appointmentsData && appointmentsData.length > 0) {
                    console.log("Appointments data available, initializing calendar");
                    updateCalendar();
                } else {
                    console.log("No appointments data available for calendar");
                    // Still initialize calendar (it will now handle empty data gracefully)
                    updateCalendar();
                }
            } catch (e) {
                console.error("Error initializing calendar:", e);
                // Try to initialize anyway, our updateCalendar function is now more robust
                updateCalendar();
            }
        });
        
        // Show calendar view
        function showCalendarView() {
            document.getElementById('calendarView').style.display = 'block';
            updateCalendar();
            
            // Scroll to calendar view
            document.getElementById('calendarView').scrollIntoView({
                behavior: 'smooth'
            });
        }
        
        // Hide calendar view
        function hideCalendarView() {
            document.getElementById('calendarView').style.display = 'none';
        }
        
        // Store appointments data for calendar
        const appointmentsData = <?php echo json_encode($appointments); ?>;
        
        // Update calendar based on selected month and year
        function updateCalendar() {
            const month = parseInt(document.getElementById('calendarMonth').value);
            const year = parseInt(document.getElementById('calendarYear').value);
            
            // Get first day of the month
            const firstDay = new Date(year, month - 1, 1);
            
            // Get last day of the month
            const lastDay = new Date(year, month, 0);
            
            // Get day of week for first day (0-6, 0 = Sunday)
            const startingDay = firstDay.getDay();
            
            // Total days in month
            const monthLength = lastDay.getDate();
            
            // Current date
            const today = new Date();
            const currentDay = today.getDate();
            const currentMonth = today.getMonth() + 1;
            const currentYear = today.getFullYear();
            
            // Calendar HTML
            let calendarHTML = '';
            
            // Row counter
            let day = 1;
            const rows = Math.ceil((monthLength + startingDay) / 7);
            
            // Generate calendar rows
            for (let i = 0; i < rows; i++) {
                calendarHTML += '<tr>';
                
                // Generate 7 cells for each day of the week
                for (let j = 0; j < 7; j++) {
                    if (i === 0 && j < startingDay) {
                        // Empty cells before the first day
                        const prevMonthLastDay = new Date(year, month - 1, 0).getDate();
                        const prevDay = prevMonthLastDay - (startingDay - j - 1);
                        calendarHTML += `<td class="other-month">
                            <div class="calendar-day">${prevDay}</div>
                        </td>`;
                    } else if (day > monthLength) {
                        // Empty cells after the last day
                        const nextDay = day - monthLength;
                        calendarHTML += `<td class="other-month">
                            <div class="calendar-day">${nextDay}</div>
                        </td>`;
                        day++;
                    } else {
                        // Regular day cell
                        const isCurrentDay = day === currentDay && month === currentMonth && year === currentYear;
                        const dayClass = isCurrentDay ? 'current-day' : '';
                        
                        // Format date string for comparison
                        const dateStr = `${year}-${month.toString().padStart(2, '0')}-${day.toString().padStart(2, '0')}`;
                        
                        // Get appointments for this day - only CONFIRMED and COMPLETED ones
                        let appointmentsHTML = '';
                        
                        // Check if appointmentsData is defined and not empty
                        if (typeof appointmentsData !== 'undefined' && appointmentsData && appointmentsData.length > 0) {
                            const dayAppointments = appointmentsData.filter(appt => {
                                // Make sure appt and appt.appt_datetime exist
                                if (!appt || !appt.appt_datetime) return false;
                                
                                const apptDate = appt.appt_datetime.split(' ')[0]; // Get just the date part
                                const status = appt.status ? appt.status.toUpperCase() : '';
                                // Only include confirmed and completed appointments (exclude pending and cancelled)
                                return apptDate === dateStr && (status === 'CONFIRMED' || status === 'COMPLETED');
                            });
                            
                            // Create appointment items for the cell
                            dayAppointments.forEach(appt => {
                                const time = new Date(appt.appt_datetime).toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
                                const statusClass = `appointment-${appt.status.toLowerCase()}`;
                                appointmentsHTML += `
                                    <div class="appointment-item ${statusClass}" 
                                         onclick="openEditModal(${JSON.stringify(appt).replace(/"/g, '&quot;')})">
                                        ${time} - ${appt.pet_name} (${appt.clt_fname})
                                    </div>
                                `;
                            });
                        }
                        
                        calendarHTML += `
                            <td>
                                <div class="calendar-day ${dayClass}">${day}</div>
                                <div class="appointment-list">
                                    ${appointmentsHTML}
                                </div>
                            </td>
                        `;
                        day++;
                    }
                }
                
                calendarHTML += '</tr>';
            }
            
            document.getElementById('calendarBody').innerHTML = calendarHTML;
        }
        
        // Filter appointments based on URL parameters
        document.addEventListener('DOMContentLoaded', function() {
            // Check if we have a filter parameter in the URL
            const urlParams = new URLSearchParams(window.location.search);
            const filter = urlParams.get('filter');
            
            if (filter) {
                // Clear any existing search
                const searchInput = document.querySelector('input[name="search"]');
                if (searchInput) {
                    searchInput.value = '';
                }
                
                // Highlight the active filter button
                const filterButton = document.getElementById(`btn-filter-${filter.toLowerCase()}`);
                if (filterButton) {
                    // Remove outline classes and add solid color classes
                    filterButton.classList.remove('btn-outline-warning', 'btn-outline-success', 
                                              'btn-outline-info', 'btn-outline-danger', 'btn-outline-primary');
                    
                    // Add appropriate solid color class based on filter type
                    switch(filter.toLowerCase()) {
                        case 'pending':
                            filterButton.classList.add('btn-warning', 'text-dark');
                            break;
                        case 'confirmed':
                            filterButton.classList.add('btn-success', 'text-white');
                            break;
                        case 'completed':
                            filterButton.classList.add('btn-info', 'text-white');
                            break;
                        case 'cancelled':
                            filterButton.classList.add('btn-danger', 'text-white');
                            break;
                        case 'upcoming':
                            filterButton.classList.add('btn-primary', 'text-white');
                            break;
                    }
                }
                
                // Apply appropriate filter based on parameter
                switch(filter.toLowerCase()) {
                    case 'pending':
                        filterAppointmentsByStatus('PENDING');
                        break;
                    case 'confirmed':
                        filterAppointmentsByStatus('CONFIRMED');
                        break;
                    case 'completed':
                        filterAppointmentsByStatus('COMPLETED');
                        break;
                    case 'cancelled':
                        filterAppointmentsByStatus('CANCELLED');
                        break;
                    case 'upcoming':
                        // Show calendar view and focus on upcoming appointments
                        showCalendarView();
                        // Also highlight upcoming appointments in the table
                        filterUpcomingAppointments();
                        break;
                    default:
                        break;
                }
            }
        });
        
        // Function to filter appointments by status
        function filterAppointmentsByStatus(status) {
            const table = document.getElementById('appointmentsTable');
            if (!table) return;
            
            // Highlight the filtered status with a message
            const filterMessage = document.createElement('div');
            filterMessage.className = 'alert alert-info mb-3';
            filterMessage.innerHTML = `<i class="bi bi-funnel-fill me-2"></i>Showing ${status.toLowerCase()} appointments only.`;
            
            const cardBody = table.closest('.card-body');
            if (cardBody) {
                const existingAlert = cardBody.querySelector('.alert');
                if (existingAlert) {
                    cardBody.removeChild(existingAlert);
                }
                cardBody.insertBefore(filterMessage, cardBody.firstChild);
            }
            
            // Filter the table rows
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Remove any "no results" row that might exist
            const noResultsRow = tbody.querySelector('tr[data-no-results]');
            if (noResultsRow) {
                tbody.removeChild(noResultsRow);
            }
            
            let visibleCount = 0;
            
            rows.forEach(row => {
                const statusCell = row.querySelector('td:nth-child(7)'); // Status column (adjust if needed)
                
                if (statusCell) {
                    const statusValue = statusCell.getAttribute('data-value');
                    
                    if (statusValue && statusValue.toUpperCase() === status) {
                        row.style.display = '';
                        visibleCount++;
                    } else {
                        row.style.display = 'none';
                    }
                }
            });
            
            // If no matching appointments found, show a message
            if (visibleCount === 0 && rows.length > 0) {
                const noResultsRow = document.createElement('tr');
                noResultsRow.setAttribute('data-no-results', 'true');
                noResultsRow.innerHTML = `<td colspan="9" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-filter-circle mb-3" style="font-size: 2rem;"></i>
                        <p>No ${status.toLowerCase()} appointments found</p>
                    </div>
                </td>`;
                tbody.appendChild(noResultsRow);
            }
        }
        
        // Function to filter upcoming appointments (next 7 days)
        function filterUpcomingAppointments() {
            const table = document.getElementById('appointmentsTable');
            if (!table) return;
            
            // Create a date range for the next 7 days
            const today = new Date();
            const nextWeek = new Date(today);
            nextWeek.setDate(today.getDate() + 7);
            
            // Format dates for display
            const todayStr = today.toLocaleDateString();
            const nextWeekStr = nextWeek.toLocaleDateString();
            
            // Highlight the filtered status with a message
            const filterMessage = document.createElement('div');
            filterMessage.className = 'alert alert-primary mb-3';
            filterMessage.innerHTML = `<i class="bi bi-calendar-event me-2"></i>Showing upcoming appointments from ${todayStr} to ${nextWeekStr}.`;
            
            const cardBody = table.closest('.card-body');
            if (cardBody) {
                const existingAlert = cardBody.querySelector('.alert');
                if (existingAlert) {
                    cardBody.removeChild(existingAlert);
                }
                cardBody.insertBefore(filterMessage, cardBody.firstChild);
            }
            
            // Filter the table rows
            const tbody = table.querySelector('tbody');
            const rows = Array.from(tbody.querySelectorAll('tr'));
            
            // Remove any "no results" row that might exist
            const noResultsRow = tbody.querySelector('tr[data-no-results]');
            if (noResultsRow) {
                tbody.removeChild(noResultsRow);
            }
            
            let visibleCount = 0;
            
            rows.forEach(row => {
                const dateCell = row.querySelector('td:nth-child(5)'); // Date & Time column
                const statusCell = row.querySelector('td:nth-child(7)'); // Status column
                
                if (dateCell && statusCell) {
                    const dateValue = dateCell.getAttribute('data-value');
                    const statusValue = statusCell.getAttribute('data-value');
                    
                    if (dateValue && statusValue) {
                        const apptDate = new Date(dateValue);
                        const isConfirmed = statusValue.toUpperCase() === 'CONFIRMED';
                        const isUpcoming = apptDate >= today && apptDate <= nextWeek;
                        
                        if (isConfirmed && isUpcoming) {
                            row.style.display = '';
                            visibleCount++;
                        } else {
                            row.style.display = 'none';
                        }
                    }
                }
            });
            
            // If no matching appointments found, show a message
            if (visibleCount === 0 && rows.length > 0) {
                const noResultsRow = document.createElement('tr');
                noResultsRow.setAttribute('data-no-results', 'true');
                noResultsRow.innerHTML = `<td colspan="9" class="text-center py-4">
                    <div class="text-muted">
                        <i class="bi bi-calendar3-week mb-3" style="font-size: 2rem;"></i>
                        <p>No upcoming appointments in the next 7 days</p>
                    </div>
                </td>`;
                tbody.appendChild(noResultsRow);
            }
        }
    </script>
</body>
</html>