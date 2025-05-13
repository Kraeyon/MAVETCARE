<!DOCTYPE html>
<html>
<head>
    <title>Patients</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
<?php include_once '../app/views/includes/navbar.php'; ?>
<div class="d-flex">
<?php include_once '../app/views/includes/sidebar.php'; ?>
<div class="flex-grow-1 p-4 mt-5">
    <h2 class="mb-4">Patient Records</h2>

    <button class="btn btn-success mb-3" data-bs-toggle="modal" data-bs-target="#addPetModal">Add New Pet</button>

    <table class="table table-bordered">
        <thead class="table-dark">
            <tr>
                <th>Pet Code</th>
                <th>Client Name</th>
                <th>Pet Name</th>
                <th>Type</th>
                <th>Breed</th>
                <th>Age</th>
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
                        <a href="/admin/patients/delete/<?= $pet['pet_code'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                        <!-- Optional: Edit button -->
                        <!--
                        <button class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#editPetModal<?= $pet['pet_code'] ?>">Edit</button>
                        -->
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Add Pet Modal -->
<div class="modal fade" id="addPetModal" tabindex="-1" aria-labelledby="addPetModalLabel" aria-hidden="true">
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
                    <option value="<?= $client['clt_code'] ?>">
                        <?= htmlspecialchars($client['clt_fname'] . ' ' . $client['clt_initial'] . '. ' . $client['clt_lname']) ?>
                    </option>
                <?php endforeach; ?>
                <option value="new">New Client</option>
            </select>
        </div>

        <div id="newClientFields" class="border p-2 rounded bg-light" style="display: none;">
            <h6>New Client Details</h6>
            <input type="text" name="new_fname" class="form-control mb-2" placeholder="First Name">
            <input type="text" name="new_initial" class="form-control mb-2" placeholder="Middle Initial">
            <input type="text" name="new_lname" class="form-control mb-2" placeholder="Last Name">
            <input type="text" name="new_contact" class="form-control mb-2" placeholder="Contact Number">
            <input type="email" name="new_email" class="form-control mb-2" placeholder="Email Address">
            <textarea name="new_address" class="form-control mb-2" placeholder="Home Address"></textarea>
        </div>

        <div class="mb-2">
            <label>Pet Name</label>
            <input type="text" name="pet_name" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Pet Type</label>
            <input type="text" name="pet_type" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Pet Breed</label>
            <input type="text" name="pet_breed" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Pet Age</label>
            <input type="number" name="pet_age" class="form-control" required>
        </div>
        <div class="mb-2">
            <label>Medical History</label>
            <textarea name="pet_med_history" class="form-control"></textarea>
        </div>
      </div>
      <div class="modal-footer">
        <button type="submit" class="btn btn-primary">Add Pet</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
      </div>
    </form>
  </div>
</div>

<script>
document.getElementById('clientSelect').addEventListener('change', function () {
    const newClientFields = document.getElementById('newClientFields');
    newClientFields.style.display = this.value === 'new' ? 'block' : 'none';
});
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
