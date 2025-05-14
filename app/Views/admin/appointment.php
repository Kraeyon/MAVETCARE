<?php
// Get controller instance from the route handler instead of manual inclusion
use App\Models\AdminAppointmentModel;
use Config\Database;

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
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Management - MavetCare</title>
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
                            <input type="text" name="search" class="form-control me-2" placeholder="Search appointments..." 
                                value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-search"></i> Search
                            </button>
                            <?php if (isset($_GET['search'])): ?>
                                <a href="/admin/appointment" class="btn btn-outline-secondary ms-2">
                                    <i class="bi bi-x-circle"></i> Clear
                                </a>
                            <?php endif; ?>
                        </form>
                    </div>
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
                                <th class="sortable" data-sort="id">ID <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="client">Client <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="pet">Pet <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="service">Service <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="datetime">Date & Time <span class="sort-icon"></span></th>
                                <th class="sortable" data-sort="type">Type <span class="sort-icon"></span></th>
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
                                            <span class="badge bg-<?php echo strtolower($appointment['status']) === 'pending' ? 'warning' : (strtolower($appointment['status']) === 'confirmed' ? 'success' : (strtolower($appointment['status']) === 'completed' ? 'info' : 'danger')); ?>">
                                                <?php echo $appointment['status']; ?>
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
                document.getElementById('status_appt_code').value = apptCode;
                document.getElementById('status_value').value = status;
                document.getElementById('statusUpdateForm').submit();
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
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    
                    // Skip if there's only one row with "No appointments found"
                    if (rows.length <= 1 && rows[0].querySelectorAll('td')[0].hasAttribute('colspan')) {
                        return;
                    }
                    
                    // Get column index based on data-sort
                    let colIndex = 0;
                    switch(dataSort) {
                        case 'id': colIndex = 0; break;
                        case 'client': colIndex = 1; break;
                        case 'pet': colIndex = 2; break;
                        case 'service': colIndex = 3; break;
                        case 'datetime': colIndex = 4; break;
                        case 'type': colIndex = 5; break;
                        case 'status': colIndex = 6; break;
                        case 'notes': colIndex = 7; break;
                        default: return;
                    }
                    
                    // Sort rows
                    rows.sort((a, b) => {
                        const cellA = a.cells[colIndex].getAttribute('data-value') || '';
                        const cellB = b.cells[colIndex].getAttribute('data-value') || '';
                        
                        // Check if values are dates or numbers
                        if (dataSort === 'datetime') {
                            // Date comparison
                            return isAsc 
                                ? new Date(cellA) - new Date(cellB) 
                                : new Date(cellB) - new Date(cellA);
                        } else if (dataSort === 'id') {
                            // Numeric comparison
                            return isAsc
                                ? parseInt(cellA) - parseInt(cellB)
                                : parseInt(cellB) - parseInt(cellA);
                        } else {
                            // Default string comparison
                            return isAsc
                                ? cellA.localeCompare(cellB)
                                : cellB.localeCompare(cellA);
                        }
                    });
                    
                    // Remove existing rows
                    while (tbody.firstChild) {
                        tbody.removeChild(tbody.firstChild);
                    }
                    
                    // Add sorted rows
                    rows.forEach(row => {
                        tbody.appendChild(row);
                    });
                });
            });
            
            // Initialize calendar if we have appointments
            if (appointmentsData && appointmentsData.length > 0) {
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
                        const dayAppointments = appointmentsData.filter(appt => {
                            const apptDate = appt.appt_datetime.split(' ')[0]; // Get just the date part
                            const status = appt.status.toUpperCase();
                            // Only include confirmed and completed appointments (exclude pending and cancelled)
                            return apptDate === dateStr && (status === 'CONFIRMED' || status === 'COMPLETED');
                        });
                        
                        // Create appointment items for the cell
                        let appointmentsHTML = '';
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
    </script>
</body>
</html>