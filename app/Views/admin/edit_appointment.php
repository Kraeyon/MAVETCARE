<?php include_once '../app/Views/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Appointment - MavetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
    <?php include_once '../app/Views/includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4" style="margin-top: 0;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-pencil-square me-2"></i>Edit Appointment</h2>
                <a href="/admin/appointments" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-left me-1"></i>Back to Appointments
                </a>
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="/admin/appointments/update">
                        <input type="hidden" name="update_appointment" value="1">
                        <input type="hidden" name="appt_code" value="<?php echo $appointment['appt_code']; ?>">
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="client_code" class="form-label">Client</label>
                                <select class="form-select" id="client_code" name="client_code" required>
                                    <option value="">Select Client</option>
                                    <?php foreach ($clients as $client): ?>
                                        <option value="<?php echo $client['clt_code']; ?>" <?php echo ($client['clt_code'] == $appointment['client_code']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($client['clt_fname'] . ' ' . $client['clt_lname']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="pet_code" class="form-label">Pet</label>
                                <select class="form-select" id="pet_code" name="pet_code" required>
                                    <option value="">Select Pet</option>
                                    <?php foreach ($pets as $pet): ?>
                                        <option value="<?php echo $pet['pet_code']; ?>" <?php echo ($pet['pet_code'] == $appointment['pet_code']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($pet['pet_name']); ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="appt_date" class="form-label">Appointment Date</label>
                                <input type="date" class="form-control" id="appt_date" name="appt_date" value="<?php echo date('Y-m-d', strtotime($appointment['preferred_date'])); ?>" required>
                            </div>
                            <div class="col-md-6">
                                <label for="appt_time" class="form-label">Appointment Time</label>
                                <input type="time" class="form-control" id="appt_time" name="appt_time" value="<?php echo date('H:i', strtotime($appointment['preferred_time'])); ?>" required>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="service_code" class="form-label">Service</label>
                                <select class="form-select" id="service_code" name="service_code" required>
                                    <option value="">Select Service</option>
                                    <?php foreach ($services as $service): ?>
                                        <option value="<?php echo $service['service_code']; ?>" <?php echo ($service['service_code'] == $appointment['service_code']) ? 'selected' : ''; ?>>
                                            <?php echo htmlspecialchars($service['service_name']); ?> (₱<?php echo number_format($service['service_fee'], 2); ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="status" class="form-label">Status</label>
                                <select class="form-select" id="status" name="status" required>
                                    <option value="pending" <?php echo (strtolower($appointment['status']) === 'pending') ? 'selected' : ''; ?>>Pending</option>
                                    <option value="confirmed" <?php echo (strtolower($appointment['status']) === 'confirmed') ? 'selected' : ''; ?>>Confirmed</option>
                                    <option value="completed" <?php echo (strtolower($appointment['status']) === 'completed') ? 'selected' : ''; ?>>Completed</option>
                                    <option value="cancelled" <?php echo (strtolower($appointment['status']) === 'cancelled') ? 'selected' : ''; ?>>Cancelled</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="appointment_type" class="form-label">Appointment Type</label>
                            <select class="form-select" id="appointment_type" name="appointment_type">
                                <option value="walk-in" <?php echo (strtolower($appointment['appointment_type']) === 'walk-in') ? 'selected' : ''; ?>>Walk-in</option>
                                <option value="scheduled" <?php echo (strtolower($appointment['appointment_type']) === 'scheduled') ? 'selected' : ''; ?>>Scheduled</option>
                                <option value="emergency" <?php echo (strtolower($appointment['appointment_type']) === 'emergency') ? 'selected' : ''; ?>>Emergency</option>
                            </select>
                        </div>
                        
                        <div class="mb-3">
                            <label for="additional_notes" class="form-label">Additional Notes</label>
                            <textarea class="form-control" id="additional_notes" name="additional_notes" rows="3"><?php echo htmlspecialchars($appointment['additional_notes']); ?></textarea>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save me-1"></i>Save Changes
                            </button>
                            <button type="button" class="btn btn-outline-danger" onclick="confirmDelete(<?php echo $appointment['appt_code']; ?>)">
                                <i class="bi bi-trash me-1"></i>Delete Appointment
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Delete Confirmation Modal -->
    <div class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this appointment? This action cannot be undone.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <form method="POST" action="/admin/appointments/delete">
                        <input type="hidden" name="delete_appointment" value="1">
                        <input type="hidden" name="appt_code" id="delete_appt_code" value="">
                        <button type="submit" class="btn btn-danger">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function confirmDelete(apptCode) {
        document.getElementById('delete_appt_code').value = apptCode;
        var deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
        deleteModal.show();
    }
    
    // Handle client change to load their pets
    document.getElementById('client_code').addEventListener('change', function() {
        const clientId = this.value;
        if (clientId) {
            // Create form data
            const formData = new FormData();
            formData.append('selected_client_id', clientId);
            
            // Send fetch request
            fetch('/admin/appointments/get-pets', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                const petDropdown = document.getElementById('pet_code');
                // Clear existing options
                petDropdown.innerHTML = '<option value="">Select Pet</option>';
                
                // Add new options
                data.forEach(pet => {
                    const option = document.createElement('option');
                    option.value = pet.pet_code;
                    option.textContent = pet.pet_name;
                    petDropdown.appendChild(option);
                });
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    });
    </script>
</body>
</html> 