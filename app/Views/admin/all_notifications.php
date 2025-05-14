<?php include_once '../app/Views/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>All Notifications - MavetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .notification-card {
            margin-bottom: 1.5rem;
            border-radius: 0.5rem;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .notification-card .card-header {
            border-bottom: none;
            font-weight: 600;
        }
        .notification-item {
            padding: 0.75rem 1rem;
            border-left: 4px solid transparent;
            transition: all 0.2s ease;
        }
        .notification-item:hover {
            background-color: rgba(0, 0, 0, 0.03);
        }
        .notification-item.warning {
            border-left-color: #ffc107;
        }
        .notification-item.danger {
            border-left-color: #dc3545;
        }
        .notification-item.success {
            border-left-color: #198754;
        }
        .notification-item.primary {
            border-left-color: #0d6efd;
        }
        .notification-item.info {
            border-left-color: #0dcaf0;
        }
        .notification-badge {
            padding: 0.25rem 0.5rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0.25rem;
        }
    </style>
</head>
<body>
    <div class="d-flex">
    <?php include_once '../app/Views/includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4" style="margin-top: 0;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-bell me-2"></i>All Notifications</h2>
                <div>
                    <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                        <i class="bi bi-arrow-clockwise me-1"></i>Refresh
                    </button>
                </div>
            </div>

            <!-- Pending Appointments Section -->
            <?php if (!empty($pendingAppointments)): ?>
            <div class="card notification-card">
                <div class="card-header bg-primary text-white">
                    <i class="bi bi-clock me-2"></i>Pending Appointments
                    <span class="badge bg-light text-primary ms-2"><?php echo count($pendingAppointments); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($pendingAppointments as $appt): ?>
                        <div class="notification-item primary">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">
                                        <?php echo htmlspecialchars($appt['pet_name']); ?> (<?php echo htmlspecialchars($appt['pet_type']); ?>)
                                        <span class="text-muted">- <?php echo htmlspecialchars($appt['clt_fname'] . ' ' . $appt['clt_lname']); ?></span>
                                    </div>
                                    <div>
                                        <small>
                                            <i class="bi bi-calendar-date me-1"></i>
                                            <?php echo date('F j, Y', strtotime($appt['preferred_date'])); ?> at 
                                            <?php echo date('g:i A', strtotime($appt['preferred_time'])); ?>
                                        </small>
                                        <?php if (!empty($appt['service_name'])): ?>
                                        <small class="ms-2">
                                            <i class="bi bi-heart-pulse me-1"></i>
                                            <?php echo htmlspecialchars($appt['service_name']); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <button class="btn btn-sm btn-success me-1" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'confirmed')">
                                        Approve
                                    </button>
                                    <button class="btn btn-sm btn-danger" onclick="updateAppointmentStatus(<?php echo $appt['appt_code']; ?>, 'cancelled')">
                                        Cancel
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="/admin/appointments?status=pending" class="text-decoration-none small">View all pending appointments</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Unpaid Transactions Section -->
            <?php if (!empty($unpaidTransactions)): ?>
            <div class="card notification-card">
                <div class="card-header bg-warning text-dark">
                    <i class="bi bi-cash-coin me-2"></i>Unpaid Transactions
                    <span class="badge bg-dark text-warning ms-2"><?php echo count($unpaidTransactions); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($unpaidTransactions as $transaction): ?>
                        <div class="notification-item warning">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">
                                        Transaction #<?php echo $transaction['transaction_code']; ?>
                                        <span class="text-muted">- <?php echo htmlspecialchars($transaction['clt_fname'] . ' ' . $transaction['clt_lname']); ?></span>
                                    </div>
                                    <div>
                                        <small>
                                            <i class="bi bi-calendar-date me-1"></i>
                                            <?php echo date('F j, Y', strtotime($transaction['transaction_datetime'])); ?>
                                        </small>
                                        <small class="ms-2">
                                            <i class="bi bi-currency-dollar me-1"></i>
                                            ₱<?php echo number_format($transaction['transaction_total_amount'], 2); ?>
                                        </small>
                                    </div>
                                </div>
                                <div>
                                    <a href="/admin/transactions/edit/<?php echo $transaction['transaction_code']; ?>" class="btn btn-sm btn-primary">
                                        Process Payment
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="/admin/transactions?payment_method=pending" class="text-decoration-none small">View all unpaid transactions</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Low Stock Products Section -->
            <?php if (!empty($lowStockProducts)): ?>
            <div class="card notification-card">
                <div class="card-header bg-danger text-white">
                    <i class="bi bi-exclamation-triangle me-2"></i>Low Stock Products
                    <span class="badge bg-light text-danger ms-2"><?php echo count($lowStockProducts); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($lowStockProducts as $product): ?>
                        <div class="notification-item danger">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">
                                        <?php echo htmlspecialchars($product['prod_name']); ?>
                                        <span class="notification-badge bg-danger text-white ms-2">
                                            <?php echo $product['prod_stock']; ?> left
                                        </span>
                                    </div>
                                    <div>
                                        <small>
                                            <i class="bi bi-box me-1"></i>
                                            <?php echo htmlspecialchars($product['prod_category']); ?>
                                        </small>
                                        <?php if (!empty($product['supp_name'])): ?>
                                        <small class="ms-2">
                                            <i class="bi bi-truck me-1"></i>
                                            Supplier: <?php echo htmlspecialchars($product['supp_name']); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <a href="/admin/inventory/restock/<?php echo $product['prod_code']; ?>" class="btn btn-sm btn-primary">
                                        Restock
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="/admin/inventory?filter=low-stock" class="text-decoration-none small">View all low stock items</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Upcoming Appointments Section -->
            <?php if (!empty($upcomingAppointments)): ?>
            <div class="card notification-card">
                <div class="card-header bg-success text-white">
                    <i class="bi bi-calendar-event me-2"></i>Upcoming Appointments
                    <span class="badge bg-light text-success ms-2"><?php echo count($upcomingAppointments); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($upcomingAppointments as $appt): ?>
                        <div class="notification-item success">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <div class="fw-bold">
                                        <?php echo htmlspecialchars($appt['pet_name']); ?> (<?php echo htmlspecialchars($appt['pet_type']); ?>)
                                        <span class="text-muted">- <?php echo htmlspecialchars($appt['clt_fname'] . ' ' . $appt['clt_lname']); ?></span>
                                    </div>
                                    <div>
                                        <small>
                                            <i class="bi bi-calendar-date me-1"></i>
                                            <?php echo date('F j, Y', strtotime($appt['preferred_date'])); ?> at 
                                            <?php echo date('g:i A', strtotime($appt['preferred_time'])); ?>
                                        </small>
                                        <?php if (!empty($appt['service_name'])): ?>
                                        <small class="ms-2">
                                            <i class="bi bi-heart-pulse me-1"></i>
                                            <?php echo htmlspecialchars($appt['service_name']); ?>
                                        </small>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <div>
                                    <a href="/admin/appointments/edit/<?php echo $appt['appt_code']; ?>" class="btn btn-sm btn-outline-secondary me-1">
                                        View
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="/admin/appointments?period=next-week" class="text-decoration-none small">View all upcoming appointments</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Recent Reviews Section -->
            <?php if (!empty($recentReviews)): ?>
            <div class="card notification-card">
                <div class="card-header bg-info text-white">
                    <i class="bi bi-star me-2"></i>Recent Reviews
                    <span class="badge bg-light text-info ms-2"><?php echo count($recentReviews); ?></span>
                </div>
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentReviews as $review): ?>
                        <div class="notification-item info">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <div class="fw-bold">
                                        <?php echo htmlspecialchars($review['clt_fname'] . ' ' . $review['clt_lname']); ?>
                                        <span class="ms-2">
                                            <?php for ($i = 0; $i < 5; $i++): ?>
                                                <?php if ($i < $review['rating']): ?>
                                                    <i class="bi bi-star-fill text-warning"></i>
                                                <?php else: ?>
                                                    <i class="bi bi-star text-muted"></i>
                                                <?php endif; ?>
                                            <?php endfor; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <small class="text-muted">
                                            <i class="bi bi-calendar-date me-1"></i>
                                            <?php echo date('F j, Y', strtotime($review['review_date'])); ?>
                                        </small>
                                    </div>
                                    <div class="mt-1">
                                        <?php echo htmlspecialchars($review['comment']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="card-footer bg-light text-center">
                    <a href="/admin/reviews" class="text-decoration-none small">View all reviews</a>
                </div>
            </div>
            <?php endif; ?>

            <?php if (empty($pendingAppointments) && empty($unpaidTransactions) && empty($lowStockProducts) && empty($upcomingAppointments) && empty($recentReviews)): ?>
            <div class="text-center my-5 py-5">
                <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                <h3 class="mt-3">No Notifications</h3>
                <p class="text-muted">You're all caught up! There are no pending notifications at this time.</p>
            </div>
            <?php endif; ?>
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
            
            // Send fetch request
            fetch('/admin/appointments/update-status', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert("Appointment status updated successfully");
                    // Reload page to reflect changes
                    window.location.reload();
                } else {
                    alert("Failed to update appointment status: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert("An error occurred while updating the appointment status");
            });
        }
    }
    </script>
</body>
</html> 