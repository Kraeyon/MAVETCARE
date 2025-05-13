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
            height: 100%;
        }
        .doctor-card:hover {
            transform: scale(1.02);
        }
        .doctor-card .card-body {
            padding: 20px;
        }
        .doctor-card .card-title {
            font-weight: bold;
            color: #333;
        }
        .doctor-card .schedule {
            white-space: pre-wrap;
            font-size: 0.9rem;
        }
        .editable {
            background-color: #f8f9fa;
            border: 1px solid #0d6efd;
        }
        .btn-edit, .btn-save {
            transition: all 0.3s ease;
            width: 100%;
            margin-top: 10px;
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
        .dashboard-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }
        .doctor-count {
            font-size: 1.1rem;
            color: #0d6efd;
            font-weight: 500;
        }
        .doctor-card .card-header {
            display: flex;
            align-items: center;
        }
        .doctor-card .card-header i {
            margin-right: 10px;
            font-size: 1.2rem;
        }
    </style>
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>

<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="dashboard-header d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-0">Doctor Staff Management</h2>
                <p class="text-muted mb-0">View and manage doctor schedules</p>
            </div>
            <div class="doctor-count">
                <i class="bi bi-people-fill"></i> Total Doctors: <span class="badge bg-primary"><?= count($doctors) ?></span>
            </div>
        </div>

        <?php if (!empty($doctors)) : ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php foreach ($doctors as $doc) : ?>
                    <div class="col">
                        <div class="card doctor-card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-person-circle"></i> <span class="fw-bold"><?= htmlspecialchars($doc->staff_name) ?></span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-award"></i> <?= htmlspecialchars($doc->staff_position) ?></h5>
                                <div class="schedule-container mt-3">
                                    <p class="text-muted mb-1"><i class="bi bi-calendar-week"></i> Schedule:</p>
                                    <form method="POST" action="/admin/doctor/edit/<?= $doc->staff_code ?>" id="edit-schedule-form-<?= $doc->staff_code ?>">
                                        <textarea name="schedule" class="form-control schedule border-light" rows="4" id="schedule-<?= $doc->staff_code ?>" readonly><?= htmlspecialchars($doc->staff_schedule) ?></textarea>
                                        
                                        <button type="button" class="btn btn-edit btn-sm" id="update-button-<?= $doc->staff_code ?>" onclick="enableEdit(<?= $doc->staff_code ?>)">
                                            <i class="bi bi-pencil"></i> Update Schedule
                                        </button>
                                        
                                        <button type="submit" class="btn btn-save btn-sm d-none" id="save-button-<?= $doc->staff_code ?>">
                                            <i class="bi bi-save"></i> Save Changes
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else : ?>
            <div class="alert alert-info">
                <i class="bi bi-info-circle"></i> No doctors found in the system.
            </div>
        <?php endif; ?>
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
        textarea.classList.add('editable');
        textarea.focus();

        // Change button text and functionality
        updateButton.classList.add('d-none');
        saveButton.classList.remove('d-none');
    }
</script>

</body>
</html>
