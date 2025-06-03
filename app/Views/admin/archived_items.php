<?php include_once '../app/Views/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Archived Items - MavetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <div class="d-flex">
        <?php include_once '../app/Views/includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4" style="margin-top: 0;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-archive-fill me-2 text-warning"></i>Archived Items</h2>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb mb-0">
                        <li class="breadcrumb-item"><a href="/index">Dashboard</a></li>
                        <li class="breadcrumb-item active" aria-current="page">Archived Items</li>
                    </ol>
                </nav>
            </div>

            <div class="card shadow-sm mb-4">
                <div class="card-body">
                    <p class="text-muted">
                        <i class="bi bi-info-circle me-2"></i>
                        This page shows all archived items. Archived items are not deleted from the system but are no longer active.
                    </p>
                </div>
            </div>

            <!-- Nav tabs -->
            <ul class="nav nav-tabs mb-4" id="archiveTab" role="tablist">
                <li class="nav-item" role="presentation">
                    <button class="nav-link active" id="appointments-tab" data-bs-toggle="tab" data-bs-target="#appointments" type="button" role="tab" aria-controls="appointments" aria-selected="true">
                        <i class="bi bi-calendar-check me-1"></i> Appointments (<?php echo count($appointments); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="products-tab" data-bs-toggle="tab" data-bs-target="#products" type="button" role="tab" aria-controls="products" aria-selected="false">
                        <i class="bi bi-box me-1"></i> Products (<?php echo count($products); ?>)
                    </button>
                </li>
                <li class="nav-item" role="presentation">
                    <button class="nav-link" id="staff-tab" data-bs-toggle="tab" data-bs-target="#staff" type="button" role="tab" aria-controls="staff" aria-selected="false">
                        <i class="bi bi-people me-1"></i> Staff (<?php echo count($staff); ?>)
                    </button>
                </li>
            </ul>
            
            <!-- Tab content -->
            <div class="tab-content" id="archiveTabContent">
                <!-- Appointments Tab -->
                <div class="tab-pane fade show active" id="appointments" role="tabpanel" aria-labelledby="appointments-tab">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Archived Appointments</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($appointments)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-calendar-x text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 text-muted">No archived appointments found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>ID</th>
                                                <th>Client</th>
                                                <th>Pet</th>
                                                <th>Service</th>
                                                <th>Date & Time</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($appointments as $appointment): ?>
                                                <tr>
                                                    <td><?php echo $appointment['appt_code']; ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['clt_fname'] . ' ' . $appointment['clt_lname']); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['pet_name'] . ' (' . $appointment['pet_type'] . ')'); ?></td>
                                                    <td><?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></td>
                                                    <td>
                                                        <?php 
                                                            $date = isset($appointment['preferred_date']) ? date('M d, Y', strtotime($appointment['preferred_date'])) : 'N/A';
                                                            $time = isset($appointment['preferred_time']) ? date('h:i A', strtotime($appointment['preferred_time'])) : 'N/A';
                                                            echo $date . ' at ' . $time;
                                                        ?>
                                                    </td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#appointmentDetailsModal<?php echo $appointment['appt_code']; ?>">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal for appointment details -->
                                                <div class="modal fade" id="appointmentDetailsModal<?php echo $appointment['appt_code']; ?>" tabindex="-1">
                                                    <div class="modal-dialog modal-lg">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Appointment Details #<?php echo $appointment['appt_code']; ?></h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <h6>Client Information</h6>
                                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($appointment['clt_fname'] . ' ' . $appointment['clt_lname']); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6>Pet Information</h6>
                                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($appointment['pet_name']); ?></p>
                                                                        <p class="mb-1"><strong>Type:</strong> <?php echo htmlspecialchars($appointment['pet_type']); ?></p>
                                                                        <p class="mb-1"><strong>Breed:</strong> <?php echo htmlspecialchars($appointment['pet_breed'] ?? 'N/A'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <h6>Appointment Information</h6>
                                                                        <p class="mb-1"><strong>Service:</strong> <?php echo htmlspecialchars($appointment['service_name'] ?? 'N/A'); ?></p>
                                                                        <p class="mb-1"><strong>Date:</strong> <?php echo isset($appointment['preferred_date']) ? date('F d, Y', strtotime($appointment['preferred_date'])) : 'N/A'; ?></p>
                                                                        <p class="mb-1"><strong>Time:</strong> <?php echo isset($appointment['preferred_time']) ? date('h:i A', strtotime($appointment['preferred_time'])) : 'N/A'; ?></p>
                                                                        <p class="mb-1"><strong>Type:</strong> <?php echo htmlspecialchars($appointment['appointment_type'] ?? 'N/A'); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <h6>Additional Information</h6>
                                                                        <p class="mb-1"><strong>Notes:</strong> <?php echo !empty($appointment['additional_notes']) ? htmlspecialchars($appointment['additional_notes']) : 'No additional notes'; ?></p>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Products Tab -->
                <div class="tab-pane fade" id="products" role="tabpanel" aria-labelledby="products-tab">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Archived Products</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($products)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-box text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 text-muted">No archived products found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Image</th>
                                                <th>Product Name</th>
                                                <th>Category</th>
                                                <th>Stock</th>
                                                <th>Price</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($products as $product): ?>
                                                <tr>
                                                    <td>
                                                        <img src="<?php echo htmlspecialchars($product['prod_image']); ?>" 
                                                             alt="<?php echo htmlspecialchars($product['prod_name']); ?>"
                                                             class="img-thumbnail"
                                                             style="width: 50px; height: 50px; object-fit: cover;">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($product['prod_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($product['prod_category']); ?></td>
                                                    <td><?php echo $product['prod_stock']; ?></td>
                                                    <td>₱<?php echo number_format($product['prod_price'], 2); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#productDetailsModal<?php echo $product['prod_code']; ?>">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal for product details -->
                                                <div class="modal fade" id="productDetailsModal<?php echo $product['prod_code']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Product Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="text-center mb-3">
                                                                    <img src="<?php echo htmlspecialchars($product['prod_image']); ?>" 
                                                                         alt="<?php echo htmlspecialchars($product['prod_name']); ?>"
                                                                         class="img-fluid"
                                                                         style="max-height: 200px;">
                                                                </div>
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($product['prod_name']); ?></p>
                                                                        <p class="mb-1"><strong>Category:</strong> <?php echo htmlspecialchars($product['prod_category']); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Stock:</strong> <?php echo $product['prod_stock']; ?></p>
                                                                        <p class="mb-1"><strong>Price:</strong> ₱<?php echo number_format($product['prod_price'], 2); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="alert alert-warning">
                                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                                    This product has been archived and is no longer active in the system.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Staff Tab -->
                <div class="tab-pane fade" id="staff" role="tabpanel" aria-labelledby="staff-tab">
                    <div class="card">
                        <div class="card-header bg-light">
                            <h5 class="mb-0">Archived Staff</h5>
                        </div>
                        <div class="card-body">
                            <?php if (empty($staff)): ?>
                                <div class="text-center py-5">
                                    <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                                    <p class="mt-3 text-muted">No archived staff members found.</p>
                                </div>
                            <?php else: ?>
                                <div class="table-responsive">
                                    <table class="table table-hover">
                                        <thead>
                                            <tr>
                                                <th>Name</th>
                                                <th>Position</th>
                                                <th>Contact</th>
                                                <th>Email</th>
                                                <th>Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php foreach ($staff as $member): ?>
                                                <tr>
                                                    <td><?php echo htmlspecialchars($member['staff_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($member['staff_position']); ?></td>
                                                    <td><?php echo htmlspecialchars($member['staff_contact']); ?></td>
                                                    <td><?php echo htmlspecialchars($member['staff_email_address']); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary" 
                                                                data-bs-toggle="modal" 
                                                                data-bs-target="#staffDetailsModal<?php echo $member['staff_code']; ?>">
                                                            <i class="bi bi-eye"></i> View
                                                        </button>
                                                    </td>
                                                </tr>
                                                
                                                <!-- Modal for staff details -->
                                                <div class="modal fade" id="staffDetailsModal<?php echo $member['staff_code']; ?>" tabindex="-1">
                                                    <div class="modal-dialog">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <h5 class="modal-title">Staff Details</h5>
                                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row mb-3">
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Name:</strong> <?php echo htmlspecialchars($member['staff_name']); ?></p>
                                                                        <p class="mb-1"><strong>Position:</strong> <?php echo htmlspecialchars($member['staff_position']); ?></p>
                                                                    </div>
                                                                    <div class="col-md-6">
                                                                        <p class="mb-1"><strong>Contact:</strong> <?php echo htmlspecialchars($member['staff_contact']); ?></p>
                                                                        <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($member['staff_email_address']); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div class="alert alert-warning">
                                                                    <i class="bi bi-exclamation-triangle me-2"></i>
                                                                    This staff member has been archived and is no longer active in the system.
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html> 