<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>MavetCare Clinic</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .doctor-card {
            margin-bottom: 20px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            transition: transform 0.2s ease;
        }
        .doctor-card:hover {
            transform: scale(1.05);
        }
        .doctor-card .card-body {
            padding: 20px;
        }
        .doctor-card .card-title {
            font-weight: bold;
        }
        .doctor-card .schedule {
            white-space: pre-wrap;
        }
        .editable {
            background-color: #f8f9fa;
        }
        .btn-edit, .btn-save {
            transition: all 0.3s ease;
        }
        .btn-edit {
            background-color: #0d6efd;
            color: white;
            border: none;
        }
        .btn-edit:hover {
            background-color: #004085;
            color: #f8f9fa;
        }
        .btn-save {
            background-color: #198754;
            color: white;
            border: none;
        }
        .btn-save:hover {
            background-color: #145a32;
        }
    </style>
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>

<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <h2>Doctor Staff List & Schedules</h2>

        <div class="row">
            <?php if (!empty($doctors)) : ?>
                <?php foreach ($doctors as $doc) : ?>
                    <div class="col-md-4">
                        <div class="card doctor-card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-person-circle"></i> <?= htmlspecialchars($doc->staff_name) ?>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($doc->staff_position) ?></h5>
                                <form method="POST" action="/admin/doctor/edit/<?= $doc->staff_code ?>" id="edit-schedule-form-<?= $doc->staff_code ?>">
                                    <!-- Initially, the textarea is disabled. -->
                                    <textarea name="schedule" class="form-control schedule" rows="4" id="schedule-<?= $doc->staff_code ?>" readonly><?= htmlspecialchars($doc->staff_schedule) ?></textarea>
                                    
                                    <!-- Update button to allow editing -->
                                    <button type="button" class="btn btn-edit btn-sm mt-3" id="update-button-<?= $doc->staff_code ?>" onclick="enableEdit(<?= $doc->staff_code ?>)">
                                        <i class="bi bi-pencil"></i> Update Schedule
                                    </button>
                                    
                                    <!-- Save button, initially hidden -->
                                    <button type="submit" class="btn btn-save btn-sm mt-3 d-none" id="save-button-<?= $doc->staff_code ?>">
                                        <i class="bi bi-save"></i> Save Changes
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else : ?>
                <p>No doctors found.</p>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
    // Function to enable editing when the Update Schedule button is clicked
    function enableEdit(staffCode) {
        var textarea = document.getElementById('schedule-' + staffCode);
        var updateButton = document.getElementById('update-button-' + staffCode);
        var saveButton = document.getElementById('save-button-' + staffCode);

        // Enable textarea for editing
        textarea.removeAttribute('readonly');
        textarea.classList.add('editable'); // Optional: to visually indicate editable state

        // Change button text and functionality
        updateButton.classList.add('d-none'); // Hide Update button
        saveButton.classList.remove('d-none'); // Show Save button
    }
</script>

</body>
</html>
