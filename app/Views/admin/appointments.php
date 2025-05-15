<?php
// Add StatusHelper at the top of the file
use App\Utils\StatusHelper;
?>

<?php include_once '../app/Views/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MavetCare Clinic - Appointments</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
</head>
<body>
    <div class="d-flex">
    <?php include_once '../app/Views/includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4" style="margin-top: 0;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <?php if (isset($filterTitle)): ?>
                        <?php echo $filterTitle; ?>
                    <?php else: ?>
                        All Appointments
                    <?php endif; ?>
                </h2>
                <div class="d-flex gap-2">
                    <div class="dropdown">
                        <button class="btn btn-outline-secondary dropdown-toggle" type="button" id="filterDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-funnel me-1"></i>Filter
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="filterDropdown">
                            <li><a class="dropdown-item" href="/admin/appointments">All Appointments</a></li>
                            <li><a class="dropdown-item" href="/admin/appointments?status=pending">Pending Appointments</a></li>
                            <li><a class="dropdown-item" href="/admin/appointments?status=confirmed">Confirmed Appointments</a></li>
                            <li><a class="dropdown-item" href="/admin/appointments?status=completed">Completed Appointments</a></li>
                            <li><a class="dropdown-item" href="/admin/appointments?status=cancelled">Cancelled Appointments</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item" href="/admin/appointments?period=next-week">Next 7 Days</a></li>
                            <li><a class="dropdown-item" href="/admin/appointments?date=<?php echo date('Y-m-d'); ?>">Today</a></li>
                        </ul>
                    </div>
                    <a href="/admin/appointment" class="btn btn-primary">
                        <i class="bi bi-arrow-left me-1"></i>Back to Appointment Dashboard
                    </a>
                    <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>

            <div class="card shadow-sm">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th>Pet</th>
                                    <th>Owner</th>
                                    <th>Date & Time</th>
                                    <th>Service</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($appointments)): ?>
                                    <?php foreach ($appointments as $appt): ?>
                                        <tr>
                                            <td>
                                                <div class="fw-semibold"><?php echo htmlspecialchars($appt['pet_name']); ?></div>
                                                <small class="text-muted"><?php echo htmlspecialchars($appt['pet_type']); ?> 
                                                <?php if (!empty($appt['pet_breed'])): ?>
                                                    (<?php echo htmlspecialchars($appt['pet_breed']); ?>)
                                                <?php endif; ?></small>
                                            </td>
                                            <td>
                                                <div><?php echo htmlspecialchars($appt['clt_fname'] . ' ' . $appt['clt_lname']); ?></div>
                                                <small class="text-muted">
                                                    <?php if (!empty($appt['clt_contact'])): ?>
                                                        <i class="bi bi-telephone me-1"></i><?php echo htmlspecialchars($appt['clt_contact']); ?>
                                                    <?php endif; ?>
                                                </small>
                                            </td>
                                            <td>
                                                <div><?php echo date('F j, Y', strtotime($appt['preferred_date'])); ?></div>
                                                <small class="text-muted"><?php echo date('g:i A', strtotime($appt['preferred_time'])); ?></small>
                                            </td>
                                            <td><?php echo htmlspecialchars($appt['service_name'] ?? 'N/A'); ?></td>
                                            <td>
                                                <span class="badge <?php echo StatusHelper::getStatusClass($appt['status']); ?>">
                                                    <?php echo StatusHelper::getDisplayStatus($appt['status']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group">
                                                    <a href="/admin/appointments/edit/<?php echo $appt['appt_code']; ?>" class="btn btn-sm btn-outline-secondary">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <?php if (strtolower($appt['status']) === 'pending'): ?>
                                                        <button class="btn btn-sm btn-outline-success" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'confirmed')">
                                                            <i class="bi bi-check-lg"></i>
                                                        </button>
                                                    <?php elseif (strtolower($appt['status']) === 'confirmed'): ?>
                                                        <button class="btn btn-sm btn-outline-info" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'completed')">
                                                            <i class="bi bi-check-circle"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                    <?php if (strtolower($appt['status']) !== 'cancelled' && strtolower($appt['status']) !== 'completed'): ?>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'cancelled')">
                                                            <i class="bi bi-x-lg"></i>
                                                        </button>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="text-muted">
                                                <i class="bi bi-calendar-x mb-3" style="font-size: 2rem;"></i>
                                                <p>No appointments found</p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
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
    </script>
</body>
</html> 