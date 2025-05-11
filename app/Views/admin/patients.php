<?php
use App\Controllers\PatientController;

$controller = new PatientController();

// Handle add patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add'])) {
    $controller->addPatient($_POST);
    header("Location: patients.php");
    exit();
}

// Handle update patient
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update'])) {
    $controller->updatePatient($_POST);
    header("Location: patients.php");
    exit();
}

// Handle delete patient
if (isset($_GET['delete'])) {
    $controller->deletePatient($_GET['delete']);
    header("Location: patients.php");
    exit();
}

$pets = $controller->listPatients();
$showForm = isset($_GET['action']) && $_GET['action'] === 'add';
$editPet = null;

if (isset($_GET['edit'])) {
    foreach ($pets as $pet) {
        if ($pet['pet_code'] == $_GET['edit']) {
            $editPet = $pet;
            break;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>MavetCare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    <?php include_once '../app/views/includes/navbar.php'; ?>
    <div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>
    <div class="container p-4">
        <h2>Pet List</h2>
        <a href="?action=add" class="btn btn-success mb-3">Add New Pet</a>
        <table class="table table-bordered">
            <thead class="table-dark">
                <tr>
                    <th>Pet Code</th>
                    <th>Client Code</th>
                    <th>Name</th>
                    <th>Type</th>
                    <th>Breed</th>
                    <th>Age</th>
                    <th>Medical History</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($pets as $pet): ?>
                <tr>
                    <td><?= $pet['pet_code'] ?></td>
                    <td><?= $pet['client_code'] ?></td>
                    <td><?= htmlspecialchars($pet['pet_name']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_type']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_breed']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_age']) ?></td>
                    <td><?= htmlspecialchars($pet['pet_med_history']) ?></td>
                    <td>
                        <a href="?edit=<?= $pet['pet_code'] ?>" class="btn btn-warning">Edit</a>
                        <a href="?delete=<?= $pet['pet_code'] ?>" class="btn btn-danger">Delete</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php if ($showForm || $editPet): ?>
            <h2><?= $editPet ? 'Edit' : 'Add New' ?> Pet</h2>
            <form method="POST" class="mt-3">
                <input type="hidden" name="pet_code" value="<?= $editPet['pet_code'] ?? '' ?>">
                <div class="mb-2">
                    <input type="number" name="client_code" placeholder="Client Code" class="form-control" value="<?= $editPet['client_code'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="pet_name" placeholder="Pet Name" class="form-control" value="<?= $editPet['pet_name'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="pet_type" placeholder="Pet Type" class="form-control" value="<?= $editPet['pet_type'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="text" name="pet_breed" placeholder="Pet Breed" class="form-control" value="<?= $editPet['pet_breed'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <input type="number" name="pet_age" placeholder="Pet Age" class="form-control" value="<?= $editPet['pet_age'] ?? '' ?>" required>
                </div>
                <div class="mb-2">
                    <textarea name="pet_med_history" placeholder="Medical History" class="form-control"><?= $editPet['pet_med_history'] ?? '' ?></textarea>
                </div>
                <button type="submit" name="<?= $editPet ? 'update' : 'add' ?>" class="btn btn-primary"><?= $editPet ? 'Update' : 'Add' ?> Pet</button>
            </form>
        <?php endif; ?>

    </div>
    </div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>