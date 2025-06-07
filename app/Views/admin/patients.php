<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Patients Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .dashboard-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .pet-count {
            font-size: 1.1rem;
            color: #0d6efd;
            font-weight: 500;
        }
        .btn-action {
            margin-right: 5px;
        }
    </style>
</head>
<body>
<?php include_once '../app/views/includes/navbar.php'; ?>
<div class="d-flex">
<?php include_once '../app/views/includes/sidebar.php'; ?>
<div class="flex-grow-1 p-4 mt-5">
    <div class="dashboard-header d-flex justify-content-between align-items-center">
        <div>
            <h2 class="mb-0">Patient Records</h2>
            <p class="text-muted mb-0">View and manage pet patients</p>
        </div>
        <div class="pet-count">
            <i class="bi bi-clipboard2-pulse"></i> Total Patients: <span class="badge bg-primary"><?= count($patients) ?></span>
        </div>
    </div>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'updated'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> Pet information has been successfully updated.
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['success']) && $_GET['success'] === 'added'): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle-fill"></i> New pet has been successfully added.
        <?php if (isset($_GET['new_client']) && $_GET['new_client'] === 'yes'): ?>
        <div class="mt-2 p-3 border rounded bg-light">
            <h6 class="mb-2"><i class="bi bi-person-plus"></i> New User Account Created</h6>
            <p class="mb-1">A user account has been created for the new client.</p>
            <?php if (isset($_GET['temp_password'])): ?>
            <div class="d-flex align-items-center">
                <span class="me-2"><strong>Temporary Password:</strong></span>
                <code class="bg-white border px-3 py-1 rounded"><?= htmlspecialchars($_GET['temp_password']) ?></code>
                <button class="btn btn-sm btn-outline-secondary ms-2" onclick="copyPassword('<?= htmlspecialchars($_GET['temp_password']) ?>')">
                    <i class="bi bi-clipboard"></i> Copy
                </button>
            </div>
            <p class="text-muted small mt-2 mb-0">Please provide this password to the client. They can use it to log in.</p>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <?php if (isset($_GET['error'])): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-triangle-fill"></i> 
        <?php 
        switch($_GET['error']) {
            case 'missing_fields':
                echo 'Required fields are missing. Please complete all required fields.';
                break;
            case 'update_failed':
                echo 'Failed to update pet information. Please try again.';
                break;
            case 'add_failed':
                echo 'Failed to add new pet. Please try again.';
                break;
            case 'duplicate_pet':
                echo 'A pet with the same name and type already exists for this client.';
                break;
            default:
                echo 'An error occurred. Please try again.';
        }
        ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    <?php endif; ?>

    <div class="row mb-3">
        <div class="col-md-6">
            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#addPetModal">
                <i class="bi bi-plus-circle"></i> Add New Pet
            </button>
        </div>
        <div class="col-md-6">
            <form action="/admin/patients" method="GET" class="d-flex">
                <input type="text" name="search" class="form-control me-2" placeholder="Search by pet code, name, type, breed or client..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-search"></i> Search
                </button>
                <?php if (isset($_GET['search'])): ?>
                    <a href="/admin/patients" class="btn btn-outline-secondary ms-2">
                        <i class="bi bi-x-circle"></i> Clear
                    </a>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <table class="table table-bordered table-hover">
        <thead class="table-dark">
            <tr>
                <th><a href="/admin/patients?sort=pet_code&order=<?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_code' && isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>" class="text-white text-decoration-none">Pet Code <?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_code') ? ($_GET['order'] == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                
                <th><a href="/admin/patients?sort=client_name&order=<?= (isset($_GET['sort']) && $_GET['sort'] == 'client_name' && isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>" class="text-white text-decoration-none">Client Name <?= (isset($_GET['sort']) && $_GET['sort'] == 'client_name') ? ($_GET['order'] == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                
                <th><a href="/admin/patients?sort=pet_name&order=<?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_name' && isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>" class="text-white text-decoration-none">Pet Name <?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_name') ? ($_GET['order'] == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                
                <th><a href="/admin/patients?sort=pet_type&order=<?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_type' && isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>" class="text-white text-decoration-none">Type <?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_type') ? ($_GET['order'] == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                
                <th><a href="/admin/patients?sort=pet_breed&order=<?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_breed' && isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>" class="text-white text-decoration-none">Breed <?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_breed') ? ($_GET['order'] == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                
                <th><a href="/admin/patients?sort=pet_age&order=<?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_age' && isset($_GET['order']) && $_GET['order'] == 'ASC') ? 'DESC' : 'ASC' ?><?= isset($_GET['search']) ? '&search='.htmlspecialchars($_GET['search']) : '' ?>" class="text-white text-decoration-none">Age <?= (isset($_GET['sort']) && $_GET['sort'] == 'pet_age') ? ($_GET['order'] == 'ASC' ? '↑' : '↓') : '' ?></a></th>
                
                <th>Medical History</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($patients as $pet): ?>
                <tr>
                    <td><?= $pet['pet_code'] ?></td>
                    <td><?= htmlspecialchars($pet['client_name']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_name']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_type']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_breed']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_age']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_med_history']) ?></td>
                    <td>
                        <button class="btn btn-info btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#viewPetModal<?= $pet['pet_code'] ?>">
                            <i class="bi bi-eye"></i> View
                        </button>
                        <button class="btn btn-primary btn-sm btn-action" data-bs-toggle="modal" data-bs-target="#editPetModal<?= $pet['pet_code'] ?>">
                            <i class="bi bi-pencil"></i> Update
                        </button>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- MODALS RENDERED BELOW THE TABLE -->
    <?php foreach ($patients as $pet): ?>
        <!-- View Modal -->
        <div class="modal fade" id="viewPetModal<?= $pet['pet_code'] ?>" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-info text-white">
                        <h5 class="modal-title"><i class="bi bi-info-circle"></i> Pet Details: <?= htmlspecialchars($pet['pet_name']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="row mb-2"><div class="col-4 fw-bold">Pet Code:</div><div class="col-8"><?= $pet['pet_code'] ?></div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Owner:</div><div class="col-8"><?= htmlspecialchars($pet['client_name']) ?></div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Pet Name:</div><div class="col-8"><?= htmlspecialchars($pet['pet_name']) ?></div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Type:</div><div class="col-8"><?= htmlspecialchars($pet['pet_type']) ?></div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Breed:</div><div class="col-8"><?= htmlspecialchars($pet['pet_breed']) ?></div></div>
                        <div class="row mb-2"><div class="col-4 fw-bold">Age:</div><div class="col-8"><?= htmlspecialchars($pet['pet_age']) ?></div></div>
                        <div class="row"><div class="col-4 fw-bold">Medical History:</div><div class="col-8"><?= htmlspecialchars($pet['pet_med_history'] ?: 'No medical history recorded') ?></div></div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div class="modal fade" id="editPetModal<?= $pet['pet_code'] ?>" tabindex="-1" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
            <div class="modal-dialog">
                <form method="POST" action="/admin/patients/update" class="modal-content">
                    <input type="hidden" name="pet_code" value="<?= $pet['pet_code'] ?>">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title"><i class="bi bi-pencil-square"></i> Update Pet: <?= htmlspecialchars($pet['pet_name']) ?></h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="fw-bold">Pet Name:</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($pet['pet_name']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Pet Type:</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($pet['pet_type']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label class="fw-bold">Pet Breed:</label>
                            <p class="form-control-plaintext"><?= htmlspecialchars($pet['pet_breed']) ?></p>
                        </div>
                        <div class="mb-3">
                            <label>Pet Age</label>
                            <input type="number" name="pet_age" class="form-control" value="<?= htmlspecialchars($pet['pet_age']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label>Medical History</label>
                            <textarea name="pet_med_history" class="form-control" rows="4"><?= htmlspecialchars($pet['pet_med_history']) ?></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">Save Changes</button>
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<!-- Add Pet Modal -->
<div class="modal fade" id="addPetModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="/admin/patients/add" class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add New Pet</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
            <label>Select Client</label>
            <select name="client_code" class="form-select" id="clientSelect" required>
                <option value="">-- Choose Client --</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= $client['clt_code'] ?>"><?= htmlspecialchars($client['clt_fname'] . ' ' . $client['clt_initial'] . '. ' . $client['clt_lname']) ?></option>
                <?php endforeach; ?>
                <option value="new">New Client</option>
            </select>
        </div>
        <div id="newClientFields" class="border p-3 rounded bg-dark text-white" style="display: none;">
            <h6 class="mb-3"><i class="bi bi-person-plus"></i> New Client Details</h6>
            <div class="alert alert-info">
                <small><i class="bi bi-info-circle"></i> A user account will be created automatically. The temporary password will be shown after submission.</small>
            </div>
            <div class="row mb-2">
                <div class="col-md-5">
                    <label class="form-label small">First Name</label>
                    <input type="text" name="new_fname" class="form-control" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label small">M.I.</label>
                    <input type="text" name="new_initial" class="form-control" maxlength="1">
                </div>
                <div class="col-md-5">
                    <label class="form-label small">Last Name</label>
                    <input type="text" name="new_lname" class="form-control" required>
                </div>
            </div>
            <div class="mb-2">
                <label class="form-label small">Contact Number</label>
                <input type="text" name="new_contact" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label small">Email Address</label>
                <input type="email" name="new_email" class="form-control" required>
            </div>
            <div class="mb-2">
                <label class="form-label small">Home Address</label>
                <textarea name="new_address" class="form-control" required rows="2"></textarea>
            </div>
        </div>
        
        <div class="mb-3 mt-3">
            <label class="form-label">Pet Information</label>
            <div class="border p-3 rounded">
                <div class="row">
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Pet Name</label>
                        <input type="text" name="pet_name" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-2">
                        <label class="form-label small">Pet Type</label>
                        <input type="text" name="pet_type" class="form-control" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-8 mb-2">
                        <label class="form-label small">Breed</label>
                        <input type="text" name="pet_breed" class="form-control" required>
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label small">Age</label>
                        <input type="number" name="pet_age" class="form-control" required>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="form-label small">Medical History</label>
                    <textarea name="pet_med_history" class="form-control" rows="3"></textarea>
                </div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button class="btn btn-primary" type="submit">Add Pet</button>
        <button class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('clientSelect').addEventListener('change', function () {
    const newClientFields = document.getElementById('newClientFields');
    newClientFields.style.display = this.value === 'new' ? 'block' : 'none';
});

function copyPassword(password) {
    // Create a temporary input element
    const tempInput = document.createElement('input');
    tempInput.value = password;
    document.body.appendChild(tempInput);
    
    // Select and copy the text
    tempInput.select();
    document.execCommand('copy');
    
    // Remove the temporary element
    document.body.removeChild(tempInput);
    
    // Show a tooltip or change the button text temporarily
    const copyBtn = event.currentTarget;
    const originalHTML = copyBtn.innerHTML;
    copyBtn.innerHTML = '<i class="bi bi-check"></i> Copied!';
    setTimeout(() => {
        copyBtn.innerHTML = originalHTML;
    }, 2000);
}
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
