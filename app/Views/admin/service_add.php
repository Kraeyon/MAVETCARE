<?php include_once '../app/Views/includes/navbar.php'; ?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Add Service - MavetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        
        .card-header {
            background-color: #2c3e50;
            color: white;
            border-radius: 10px 10px 0 0 !important;
            font-weight: bold;
        }
        
        .btn-primary {
            background-color: #2c3e50;
            border-color: #2c3e50;
        }
        
        .btn-primary:hover {
            background-color: #1a252f;
            border-color: #1a252f;
        }
        
        .table th {
            background-color: #f8f9fa;
        }
        
        /* Sorting styles */
        th.sortable {
            position: relative;
            cursor: pointer;
        }
        
        th.sortable i {
            font-size: 0.8rem;
            margin-left: 5px;
            color: #adb5bd;
        }
        
        th.sortable.asc i:before {
            content: "\F12F"; /* Bootstrap Icons: sort-up */
            color: #0d6efd;
        }
        
        th.sortable.desc i:before {
            content: "\F143"; /* Bootstrap Icons: sort-down */
            color: #0d6efd;
        }
    </style>
</head>
<body>
    <div class="d-flex">
        <?php include_once '../app/Views/includes/sidebar.php'; ?>
        
        <div class="flex-grow-1 p-4" style="margin-top: 0;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="bi bi-gear-fill me-2"></i>Manage Services</h2>
            </div>
            
            <?php if (isset($message)): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <!-- Add Service Form -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-header py-3">
                            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Add New Service</h5>
                        </div>
                        <div class="card-body">
                            <form action="/admin/services/add" method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="service_name" class="form-label">Service Name</label>
                                    <input type="text" class="form-control" id="service_name" name="service_name" required>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="service_desc" class="form-label">Description</label>
                                    <textarea class="form-control" id="service_desc" name="service_desc" rows="3"></textarea>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="service_fee" class="form-label">Service Fee (₱)</label>
                                    <input type="number" class="form-control" id="service_fee" name="service_fee" step="0.01" min="0" value="0" required>
                                    <div class="form-text">Enter the price in Philippine Peso (₱)</div>
                                </div>
                                
                                <div class="mb-3">
                                    <label for="service_img" class="form-label">Service Image</label>
                                    <input type="file" class="form-control" id="service_img" name="service_img" accept="image/*">
                                    <div class="form-text">Recommended size: 800x600px. Max size: 2MB</div>
                                </div>
                                
                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="bi bi-save me-2"></i>Save Service
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <!-- Services Table -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header py-3">
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="mb-0"><i class="bi bi-list-ul me-2"></i>Current Services</h5>
                                <form action="" method="GET" class="d-flex">
                                    <div class="input-group">
                                        <input type="text" name="search" class="form-control" 
                                            placeholder="Search by name, description, fee or ID..." 
                                            value="<?= isset($search) ? htmlspecialchars($search) : '' ?>"
                                            style="height: 38px;">
                                        <button type="submit" class="btn btn-primary">
                                            <i class="bi bi-search"></i> Search
                                        </button>
                                        <?php if (isset($search) && $search): ?>
                                            <a href="/admin/services/add" class="btn btn-outline-secondary">
                                                <i class="bi bi-x-lg"></i> Clear
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </form>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-hover" id="servicesTable">
                                    <thead>
                                        <tr>
                                            <th class="sortable" data-sort="code">Code <i class="bi bi-arrow-down-up"></i></th>
                                            <th>Image</th>
                                            <th class="sortable" data-sort="name">Service Name <i class="bi bi-arrow-down-up"></i></th>
                                            <th>Description</th>
                                            <th class="sortable" data-sort="fee">Fee (₱) <i class="bi bi-arrow-down-up"></i></th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php if (!empty($services)): ?>
                                            <?php foreach ($services as $service): ?>
                                                <tr>
                                                    <td><?php echo $service['service_code']; ?></td>
                                                    <td>
                                                        <img src="<?php echo isset($service['service_img']) ? $service['service_img'] : '/assets/images/services/default.png'; ?>" 
                                                             class="img-thumbnail" alt="<?php echo htmlspecialchars($service['service_name']); ?>"
                                                             style="width: 50px; height: 50px; object-fit: cover;"
                                                             onerror="this.src='/assets/images/services/default.png'; this.onerror=null;">
                                                    </td>
                                                    <td><?php echo htmlspecialchars($service['service_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($service['service_desc']); ?></td>
                                                    <td>₱<?php echo number_format($service['service_fee'], 2); ?></td>
                                                    <td>
                                                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editService(<?php echo htmlspecialchars(json_encode($service)); ?>)">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        <button class="btn btn-sm btn-outline-danger" onclick="confirmArchiveService(<?php echo $service['service_code']; ?>, '<?php echo htmlspecialchars($service['service_name']); ?>')">
                                                            <i class="bi bi-archive"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="6" class="text-center">
                                                    <?php if (isset($search) && $search): ?>
                                                        No services found matching "<?= htmlspecialchars($search) ?>". 
                                                        <a href="/admin/services/add">View all services</a>
                                                    <?php else: ?>
                                                        No services found
                                                    <?php endif; ?>
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
        </div>
    </div>
    
    <!-- Edit Service Modal -->
    <div class="modal fade" id="editServiceModal" tabindex="-1" aria-labelledby="editServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="editServiceModalLabel">Edit Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="editServiceForm" action="/admin/services/update" method="POST" enctype="multipart/form-data">
                        <input type="hidden" id="edit_service_code" name="service_code">
                        
                        <div class="mb-3">
                            <label for="edit_service_name" class="form-label">Service Name</label>
                            <input type="text" class="form-control" id="edit_service_name" name="service_name" required>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_service_desc" class="form-label">Description</label>
                            <textarea class="form-control" id="edit_service_desc" name="service_desc" rows="3"></textarea>
                        </div>
                        
                        <div class="mb-3">
                            <label for="edit_service_fee" class="form-label">Service Fee (₱)</label>
                            <input type="number" class="form-control" id="edit_service_fee" name="service_fee" step="0.01" min="0" value="0" required>
                            <div class="form-text">Enter the price in Philippine Peso (₱)</div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Current Image</label>
                            <div class="text-center mb-2">
                                <img id="edit_current_image" src="/assets/images/services/default.png" 
                                     class="img-thumbnail" style="max-height: 150px; max-width: 100%;"
                                     onerror="this.src='/assets/images/services/default.png'; this.onerror=null;">
                            </div>
                            <label for="edit_service_img" class="form-label">Update Image</label>
                            <input type="file" class="form-control" id="edit_service_img" name="service_img" accept="image/*">
                            <div class="form-text">Leave empty to keep current image.</div>
                        </div>
                        
                        <input type="hidden" id="edit_current_img_path" name="current_img_path">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('editServiceForm').submit()">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Archive Confirmation Modal -->
    <div class="modal fade" id="archiveServiceModal" tabindex="-1" aria-labelledby="archiveServiceModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="archiveServiceModalLabel"><i class="bi bi-exclamation-triangle-fill me-2"></i>Archive Service</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to archive the service <strong id="serviceNameToArchive"></strong>?</p>
                    <p>This service will no longer appear in the active services list but can be restored from the archived items page.</p>
                    <form id="archiveServiceForm" action="/admin/services/archive" method="POST">
                        <input type="hidden" id="service_id_to_archive" name="service_id">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" onclick="document.getElementById('archiveServiceForm').submit()">
                        <i class="bi bi-archive me-1"></i>Archive Service
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Show notification toast if message is present
        document.addEventListener('DOMContentLoaded', function() {
            <?php if (isset($message)): ?>
            // Create toast notification
            const toast = document.createElement('div');
            toast.className = 'position-fixed bottom-0 end-0 p-3';
            toast.style.zIndex = '1050';
            toast.innerHTML = `
                <div class="toast show" role="alert" aria-live="assertive" aria-atomic="true">
                    <div class="toast-header bg-success text-white">
                        <strong class="me-auto"><i class="bi bi-check-circle"></i> Success</strong>
                        <button type="button" class="btn-close" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                    <div class="toast-body">
                        <?php echo $message; ?>
                    </div>
                </div>
            `;
            document.body.appendChild(toast);
            
            // Auto-dismiss after 3 seconds
            setTimeout(() => {
                const bsToast = new bootstrap.Toast(toast.querySelector('.toast'));
                bsToast.hide();
            }, 3000);
            <?php endif; ?>
        });
        
        function editService(service) {
            // Populate the edit form with service data
            document.getElementById('edit_service_code').value = service.service_code;
            document.getElementById('edit_service_name').value = service.service_name;
            document.getElementById('edit_service_desc').value = service.service_desc;
            document.getElementById('edit_service_fee').value = service.service_fee;
            
            // Handle the image
            if (service.service_img) {
                document.getElementById('edit_current_image').src = service.service_img;
                document.getElementById('edit_current_img_path').value = service.service_img;
            } else {
                document.getElementById('edit_current_image').src = '/assets/images/services/default.png';
                document.getElementById('edit_current_img_path').value = '/assets/images/services/default.png';
            }
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('editServiceModal'));
            modal.show();
        }
        
        // Function to confirm archiving a service
        function confirmArchiveService(serviceId, serviceName) {
            document.getElementById('serviceNameToArchive').textContent = serviceName;
            document.getElementById('service_id_to_archive').value = serviceId;
            
            const modal = new bootstrap.Modal(document.getElementById('archiveServiceModal'));
            modal.show();
        }
        
        // Preview uploaded image before submission (for add form)
        document.getElementById('service_img').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // You could add image preview here if desired
                    console.log('New image selected');
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Preview uploaded image before submission (for edit form)
        document.getElementById('edit_service_img').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file && file.type.match('image.*')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('edit_current_image').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
        
        // Table sorting functionality
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('servicesTable');
            if (!table) return;
            
            const headers = table.querySelectorAll('th.sortable');
            headers.forEach(header => {
                header.style.cursor = 'pointer';
                header.addEventListener('click', function() {
                    const sortType = this.getAttribute('data-sort');
                    const tbody = table.querySelector('tbody');
                    const rows = Array.from(tbody.querySelectorAll('tr'));
                    
                    // Toggle sort direction
                    const isAscending = !this.classList.contains('asc');
                    
                    // Remove sorting classes from all headers
                    headers.forEach(h => h.classList.remove('asc', 'desc'));
                    
                    // Add sort direction class to the clicked header
                    this.classList.add(isAscending ? 'asc' : 'desc');
                    
                    // Sort rows
                    rows.sort((a, b) => {
                        let valueA, valueB;
                        
                        if (sortType === 'code') {
                            valueA = parseInt(a.cells[0].textContent.trim()) || 0;
                            valueB = parseInt(b.cells[0].textContent.trim()) || 0;
                        } else if (sortType === 'name') {
                            valueA = a.cells[2].textContent.trim().toLowerCase();
                            valueB = b.cells[2].textContent.trim().toLowerCase();
                        } else if (sortType === 'fee') {
                            valueA = parseFloat(a.cells[4].textContent.replace('₱', '').replace(',', '')) || 0;
                            valueB = parseFloat(b.cells[4].textContent.replace('₱', '').replace(',', '')) || 0;
                        }
                        
                        if (isAscending) {
                            return valueA > valueB ? 1 : -1;
                        } else {
                            return valueA < valueB ? 1 : -1;
                        }
                    });
                    
                    // Clear and re-append sorted rows
                    rows.forEach(row => tbody.appendChild(row));
                });
            });
        });
        
        // Table styling
        document.addEventListener('DOMContentLoaded', function() {
            const table = document.getElementById('servicesTable');
            if (!table) return;
            
            // Add hover effect to sortable columns
            const sortableHeaders = table.querySelectorAll('th.sortable');
            sortableHeaders.forEach(header => {
                header.addEventListener('mouseover', function() {
                    this.style.backgroundColor = '#f8f9fa';
                });
                header.addEventListener('mouseout', function() {
                    this.style.backgroundColor = '';
                });
            });
        });
    </script>
</body>
</html> 