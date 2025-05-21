<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Supplier Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>
<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>
    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
            <h1 class="h2">Supplier Management</h1>
            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSupplierModal">
                <i class="bi bi-plus-lg"></i> Add New Supplier
            </button>
        </div>

        <!-- Success Messages -->
        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Supplier added successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Supplier updated successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill me-2"></i>Supplier deleted successfully!
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Error Messages -->
        <?php if (isset($_GET['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill me-2"></i>
                <?php
                switch ($_GET['error']) {
                    case 'no_supplier_code':
                        echo 'Error: No supplier code provided.';
                        break;
                    case 'add_failed':
                        echo 'Error: Failed to add the supplier.';
                        break;
                    case 'update_failed':
                        echo 'Error: Failed to update the supplier.';
                        break;
                    case 'delete_failed':
                        echo 'Error: Failed to delete the supplier.';
                        break;
                    default:
                        echo 'An error occurred.';
                }
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Suppliers Table -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light">
                            <tr>
                                <th>Code</th>
                                <th>Name</th>
                                <th>Contact Person</th>
                                <th>Contact Number</th>
                                <th>Email</th>
                                <th>Products Supplied</th>
                                <th>Product Count</th>
                                <th>Total Stock</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($suppliers as $supplier): ?>
                                <tr>
                                    <td><?= htmlspecialchars($supplier['supp_code']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supp_name']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supp_contact_person']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supp_contact_number']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supp_email_address']) ?></td>
                                    <td><?= htmlspecialchars($supplier['supp_product_supplied']) ?></td>
                                    <td><span class="badge bg-info"><?= $supplier['product_count'] ?></span></td>
                                    <td><span class="badge bg-success"><?= $supplier['total_stock'] ?? 0 ?></span></td>
                                    <td>
                                        <div class="btn-group">
                                            <button type="button" class="btn btn-sm btn-outline-primary" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#editSupplierModal"
                                                    data-supplier='<?= htmlspecialchars(json_encode($supplier)) ?>'>
                                                <i class="bi bi-pencil"></i>
                                            </button>
                                            <form method="POST" action="/admin/suppliers/delete" class="d-inline">
                                                <input type="hidden" name="supp_code" value="<?= $supplier['supp_code'] ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Are you sure you want to delete this supplier?')">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Supplier Modal -->
<div class="modal fade" id="addSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>Add New Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/suppliers/add">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="supp_name" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="supp_name" name="supp_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="supp_contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="supp_contact_person" name="supp_contact_person" required>
                    </div>
                    <div class="mb-3">
                        <label for="supp_contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="supp_contact_number" name="supp_contact_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="supp_email_address" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="supp_email_address" name="supp_email_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="supp_product_supplied" class="form-label">Products Supplied</label>
                        <textarea class="form-control" id="supp_product_supplied" name="supp_product_supplied" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Supplier Modal -->
<div class="modal fade" id="editSupplierModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title"><i class="bi bi-pencil-square me-2"></i>Edit Supplier</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="/admin/suppliers/update">
                <div class="modal-body">
                    <input type="hidden" id="edit_supp_code" name="supp_code">
                    <div class="mb-3">
                        <label for="edit_supp_name" class="form-label">Supplier Name</label>
                        <input type="text" class="form-control" id="edit_supp_name" name="supp_name" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_supp_contact_person" class="form-label">Contact Person</label>
                        <input type="text" class="form-control" id="edit_supp_contact_person" name="supp_contact_person" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_supp_contact_number" class="form-label">Contact Number</label>
                        <input type="text" class="form-control" id="edit_supp_contact_number" name="supp_contact_number" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_supp_email_address" class="form-label">Email Address</label>
                        <input type="email" class="form-control" id="edit_supp_email_address" name="supp_email_address" required>
                    </div>
                    <div class="mb-3">
                        <label for="edit_supp_product_supplied" class="form-label">Products Supplied</label>
                        <textarea class="form-control" id="edit_supp_product_supplied" name="supp_product_supplied" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Supplier</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit modal data population
    const editModal = document.getElementById('editSupplierModal');
    editModal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget;
        const supplier = JSON.parse(button.getAttribute('data-supplier'));
        
        document.getElementById('edit_supp_code').value = supplier.supp_code;
        document.getElementById('edit_supp_name').value = supplier.supp_name;
        document.getElementById('edit_supp_contact_person').value = supplier.supp_contact_person;
        document.getElementById('edit_supp_contact_number').value = supplier.supp_contact_number;
        document.getElementById('edit_supp_email_address').value = supplier.supp_email_address;
        document.getElementById('edit_supp_product_supplied').value = supplier.supp_product_supplied;
    });
});
</script>

<style>
/* Custom styles for the supplier management page */
.card {
    border: none;
    border-radius: 10px;
}

.table > :not(caption) > * > * {
    padding: 1rem;
}

.table thead th {
    font-weight: 600;
    text-transform: uppercase;
    font-size: 0.85rem;
    letter-spacing: 0.5px;
}

.btn-group {
    gap: 0.5rem;
}

.badge {
    padding: 0.5em 0.8em;
    font-weight: 500;
}

.modal-content {
    border: none;
    border-radius: 10px;
}

.modal-header {
    border-radius: 10px 10px 0 0;
}

.modal-footer {
    border-radius: 0 0 10px 10px;
}

.form-control:focus {
    border-color: #2c3e50;
    box-shadow: 0 0 0 0.2rem rgba(44, 62, 80, 0.25);
}

.btn-primary {
    background-color: #2c3e50;
    border-color: #2c3e50;
}

.btn-primary:hover {
    background-color: #34495e;
    border-color: #34495e;
}

.btn-outline-primary {
    color: #2c3e50;
    border-color: #2c3e50;
}

.btn-outline-primary:hover {
    background-color: #2c3e50;
    border-color: #2c3e50;
}

.alert {
    border: none;
    border-radius: 10px;
}

.alert-success {
    background-color: #d4edda;
    color: #155724;
}

.alert-danger {
    background-color: #f8d7da;
    color: #721c24;
}
</style>

</body>
</html> 