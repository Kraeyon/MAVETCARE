<?php
// Include database connection
include _DIR_ . '/../../config/db_connect.php';

// Initialize variables
$message = "";
$error = "";

// Handle appointment actions (add, edit, delete)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Add new appointment
    if (isset($_POST['add_appointment'])) {
        $client_code = pg_escape_string($conn, $_POST['client_code']);
        $pet_code = pg_escape_string($conn, $_POST['pet_code']);
        $service_code = pg_escape_string($conn, $_POST['service_code']);
        $appt_datetime = pg_escape_string($conn, $_POST['appt_date'] . ' ' . $_POST['appt_time']);
        $appt_type = pg_escape_string($conn, $_POST['appt_type']);
        $appt_status = pg_escape_string($conn, $_POST['appt_status']);
        $additional_notes = pg_escape_string($conn, $_POST['additional_notes']);
        
        $query = "INSERT INTO appointment (client_code, pet_code, service_code, appt_datetime, appt_type, appt_status, additional_notes) 
                    VALUES ('$client_code', '$pet_code', '$service_code', '$appt_datetime', '$appt_type', '$appt_status', '$additional_notes')";
            
                    $result = pg_query($conn, $query);
                    if ($result) {
                        $message = "Appointment added successfully!";
                    } else {
                        $error = "Error: " . pg_last_error($conn);
                    }
    }
    
    // Update existing appointment
    if (isset($_POST['update_appointment'])) {
        $appt_code = pg_escape_string($conn, $_POST['appt_code']);
        $client_code = pg_escape_string($conn, $_POST['client_code']);
        $pet_code = pg_escape_string($conn, $_POST['pet_code']);
        $service_code = pg_escape_string($conn, $_POST['service_code']);
        $appt_datetime = pg_escape_string($conn, $_POST['appt_date'] . ' ' . $_POST['appt_time']);
        $appt_type = pg_escape_string($conn, $_POST['appt_type']);
        $appt_status = pg_escape_string($conn, $_POST['appt_status']);
        $additional_notes = pg_escape_string($conn, $_POST['additional_notes']);
        
        $query = "UPDATE appointment 
                    SET client_code='$client_code', pet_code='$pet_code', service_code='$service_code', 
                        appt_datetime='$appt_datetime', appt_type='$appt_type', appt_status='$appt_status', 
                        additional_notes='$additional_notes' 
                    WHERE appt_code='$appt_code'";
            
        $result = pg_query($conn, $query);
        if ($result) {
            $message = "Appointment updated successfully!";
        } else {
            $error = "Error: " . pg_last_error($conn);
        }
    }
    
    // Delete appointment
    if (isset($_POST['delete_appointment'])) {
        $appt_code = pg_escape_string($conn, $_POST['appt_code']);
        
        $query = "DELETE FROM appointment WHERE appt_code='$appt_code'";
        
        $result = pg_query($conn, $query);
        if ($result) {
            $message = "Appointment deleted successfully!";
        } else {
            $error = "Error: " . pg_last_error($conn);
        }
    }
}

// Get all appointments with related data
$query = "SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, p.pet_breed, s.service_name, s.service_fee 
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            JOIN service s ON a.service_code = s.service_code
            ORDER BY a.appt_datetime";
            $result = pg_query($conn, $query);
            $appointments = [];
            while ($row = pg_fetch_assoc($result)) {
                $appointments[] = $row;
            }

// Get all services for dropdown
$query = "SELECT service_code, service_name, service_fee FROM service ORDER BY service_name";
$services_result = pg_query($conn, $query);
$services = [];
while ($row = pg_fetch_assoc($services_result)) {
    $services[] = $row;
}

// Close database connection
pg_close($conn);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointments Management - MavetCare</title>
    <link rel="stylesheet" href="../assets/css/admin_appointment.css">
    
</head>
<body>
    <div class="container">
        <h1>Appointments Management</h1>
        
        <?php if($message): ?>
        <div class="alert alert-success">
            <?php echo $message; ?>
        </div>
        <?php endif; ?>
        
        <?php if($error): ?>
        <div class="alert alert-danger">
            <?php echo $error; ?>
        </div>
        <?php endif; ?>
        
        <div class="card">
            <h2>Add New Appointment</h2>
            <button onclick="document.getElementById('addAppointmentModal').style.display='block'" class="btn btn-primary">Add New Appointment</button>
        </div>
        
        <div class="card">
            <h2>Appointments List</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Client</th>
                        <th>Pet</th>
                        <th>Service</th>
                        <th>Date & Time</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Notes</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($appointments) > 0): ?>
                        <?php foreach($appointments as $appointment): ?>
                            <tr>
                                <td><?php echo $appointment['appt_code']; ?></td>
                                <td><?php echo $appointment['clt_fname'] . ' ' . $appointment['clt_lname']; ?></td>
                                <td><?php echo $appointment['pet_name'] . ' (' . $appointment['pet_breed'] . ')'; ?></td>
                                <td><?php echo $appointment['service_name'] . ' - ₱' . number_format($appointment['service_fee'], 2); ?></td>
                                <td><?php echo date('M d, Y h:i A', strtotime($appointment['appt_datetime'])); ?></td>
                                <td><?php echo $appointment['appt_type']; ?></td>
                                <td>
                                    <span class="status-<?php echo strtolower($appointment['appt_status']); ?>">
                                        <?php echo $appointment['appt_status']; ?>
                                    </span>
                                </td>
                                <td><?php echo substr($appointment['additional_notes'], 0, 30) . (strlen($appointment['additional_notes']) > 30 ? '...' : ''); ?></td>
                                <td>
                                    <button class="btn btn-warning" onclick="openEditModal(<?php echo htmlentities(json_encode($appointment)); ?>)">Edit</button>
                                    <button class="btn btn-danger" onclick="openDeleteModal(<?php echo $appointment['appt_code']; ?>)">Delete</button>
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
                                <?php echo $client['clt_fname'] . ' ' . $client['clt_lname'] . ' (' . $client['clt_contact'] . ')'; ?>
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
                    <select name="appt_type" id="appt_type" class="form-control" required>
                        <option value="WALK-IN">WALK-IN</option>
                        <option value="SERVICE-ON-CALL">SERVICE-ON-CALL</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="appt_status">Status:</label>
                    <select name="appt_status" id="appt_status" class="form-control" required>
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
                                <?php echo $client['clt_fname'] . ' ' . $client['clt_lname'] . ' (' . $client['clt_contact'] . ')'; ?>
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
                    <select name="appt_type" id="edit_appt_type" class="form-control" required>
                        <option value="WALK-IN">WALK-IN</option>
                        <option value="SERVICE-ON-CALL">SERVICE-ON-CALL</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="edit_appt_status">Status:</label>
                    <select name="appt_status" id="edit_appt_status" class="form-control" required>
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
    
    <!-- Delete Appointment Modal -->
    <div id="deleteAppointmentModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="document.getElementById('deleteAppointmentModal').style.display='none'">&times;</span>
            <h2>Delete Appointment</h2>
            <p>Are you sure you want to delete this appointment? This action cannot be undone.</p>
            <form method="POST" action="">
                <input type="hidden" name="appt_code" id="delete_appt_code">
                <button type="submit" name="delete_appointment" class="btn btn-danger">Delete</button>
                <button type="button" class="btn" onclick="document.getElementById('deleteAppointmentModal').style.display='none'">Cancel</button>
            </form>
        </div>
    </div>
    
    <script>
        // Load pets based on selected client
        function loadClientPets(clientCode) {
            if (clientCode === '') {
                document.getElementById('pet_code').innerHTML = '<option value="">Select Client First</option>';
                return;
            }
            
            // AJAX request to get pets for the selected client
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_client_pets.php?client_code=' + clientCode, true);
            
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('pet_code').innerHTML = this.responseText;
                }
            };
            
            xhr.send();
        }
        
        // Load pets based on selected client for edit form
        function loadClientPetsForEdit(clientCode) {
            if (clientCode === '') {
                document.getElementById('edit_pet_code').innerHTML = '<option value="">Select Client First</option>';
                return;
            }
            
            // AJAX request to get pets for the selected client
            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'get_client_pets.php?client_code=' + clientCode, true);
            
            xhr.onload = function() {
                if (this.status === 200) {
                    document.getElementById('edit_pet_code').innerHTML = this.responseText;
                }
            };
            
            xhr.send();
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
            
            document.getElementById('edit_appt_type').value = appointment.appt_type;
            document.getElementById('edit_appt_status').value = appointment.appt_status;
            document.getElementById('edit_additional_notes').value = appointment.additional_notes;
            
            document.getElementById('editAppointmentModal').style.display = 'block';
        }
        
        // Open delete modal
        function openDeleteModal(apptCode) {
            document.getElementById('delete_appt_code').value = apptCode;
            document.getElementById('deleteAppointmentModal').style.display = 'block';
        }
        
        // Close modals when clicking outside of them
        window.onclick = function(event) {
            if (event.target.className == 'modal') {
                event.target.style.display = 'none';
            }
        }
    </script>
</body>
</html>