<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Doctor Staff Managemet</title>
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
        .search-box {
            position: relative;
            max-width: 300px;
        }
        .search-box .form-control {
            padding-left: 35px;
            border-radius: 20px;
        }
        .search-box .search-icon {
            position: absolute;
            left: 12px;
            top: 10px;
            color: #6c757d;
        }
        .schedule-display {
            max-height: 100px;
            overflow-y: auto;
            padding: 6px 10px;
            background-color: #f8f9fa;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            font-size: 1em;
            font-weight: 400;
            color: #222;
        }
        .schedule-block {
            background: #e9ecef;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            margin-bottom: 4px;
            padding: 4px 10px;
            font-size: 1em;
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 400;
        }
        .schedule-block b {
            font-weight: 600;
        }
        .schedule-block:last-child {
            margin-bottom: 0;
        }
        .schedule-block i {
            color: #0d6efd;
            font-size: 1.1em;
        }
    </style>
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>

<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>
    
    <div class="flex-grow-1 p-4">
        <div class="dashboard-header d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="mb-0">Doctor Staff Management</h2>
                <p class="text-muted mb-0">View and manage doctor schedules</p>
            </div>
            <div class="search-box mt-2 mt-md-0">
                <div class="position-relative">
                    <i class="bi bi-search search-icon"></i>
                    <input type="text" id="searchDoctor" class="form-control" 
                           placeholder="Search doctors..." value="<?= isset($search_term) ? htmlspecialchars($search_term) : '' ?>">
                    <?php if (isset($search_term) && !empty($search_term)): ?>
                        <a href="/admin/doctor" class="btn btn-outline-secondary ms-2 mt-2" style="font-size: 0.9rem;">
                            <i class="bi bi-x-lg"></i> Clear Search
                        </a>
                    <?php endif; ?>
                </div>
            </div>
            <div class="doctor-count mt-2 mt-md-0">
                <i class="bi bi-people-fill"></i> Total Doctors: <span class="badge bg-primary"><?= count($doctors) ?></span>
            </div>
        </div>

        <?php
        require_once '../config/database.php';
        use Config\Database;

        $pdo = Database::getInstance()->getConnection();

        // Fetch all doctors
        $stmt = $pdo->query("SELECT * FROM veterinary_staff WHERE staff_position = 'Doctor' ORDER BY staff_name ASC");
        $doctors = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // For each doctor, fetch their schedule as an array of rows
        foreach ($doctors as &$doc) {
            $stmt2 = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM staff_schedule WHERE staff_code = ?");
            $stmt2->execute([$doc['staff_code']]);
            $doc['schedule_details'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
        }
        unset($doc);

        // Function to format schedule for display
        function formatSchedule($scheduleDetails) {
            if (!$scheduleDetails || count($scheduleDetails) === 0) {
                return '<span class="text-muted"><i class="bi bi-calendar-x"></i> No schedule set</span>';
            }
            // Parse details into [day => [start, end]]
            $parsed = [];
            foreach ($scheduleDetails as $detail) {
                $parsed[$detail['day_of_week']] = substr($detail['start_time'], 0, 5) . '-' . substr($detail['end_time'], 0, 5);
            }
            // Group days with the same schedule
            $groups = [];
            $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
            $current = null;
            $group = [];
            foreach ($days as $day) {
                $sched = isset($parsed[$day]) ? $parsed[$day] : null;
                if ($sched !== $current) {
                    if ($group) {
                        $groups[] = [$group, $current];
                    }
                    $group = [$day];
                    $current = $sched;
                } else {
                    $group[] = $day;
                }
            }
            if ($group) {
                $groups[] = [$group, $current];
            }
            // Format output
            $out = '<div class="schedule-display">';
            foreach ($groups as [$groupDays, $sched]) {
                if (!$sched) continue;
                $label = count($groupDays) > 1 ? '<b>' . $groupDays[0] . '–' . end($groupDays) . '</b>' : '<b>' . $groupDays[0] . '</b>';
                $out .= '<div class="schedule-block"><i class="bi bi-clock"></i> ' . $label . ': <span class="text-primary">' . $sched . '</span></div>';
            }
            $out .= '</div>';
            return $out;
        }
        ?>

        <?php if (!empty($doctors)) : ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4" id="doctorsContainer">
                <?php foreach ($doctors as $doc) : ?>
                    <div class="col doctor-item" data-name="<?= strtolower(htmlspecialchars($doc['staff_name'])) ?>" data-position="<?= strtolower(htmlspecialchars($doc['staff_position'])) ?>">
                        <div class="card doctor-card">
                            <div class="card-header bg-primary text-white">
                                <i class="bi bi-person-circle"></i> <span class="fw-bold"><?= htmlspecialchars($doc['staff_name']) ?></span>
                            </div>
                            <div class="card-body">
                                <h5 class="card-title"><i class="bi bi-award"></i> <?= htmlspecialchars($doc['staff_position']) ?></h5>
                                <div class="schedule-container mt-3">
                                    <p class="text-muted mb-1"><i class="bi bi-calendar-week"></i> Schedule:</p>
                                    <?= formatSchedule($doc['schedule_details']) ?>
                                </div>
                                <button class="btn btn-sm btn-warning mt-3" data-bs-toggle="modal" data-bs-target="#editDoctorModal<?= $doc['staff_code'] ?>">
                                    <i class="bi bi-pencil"></i> Edit Schedule
                                </button>
                            </div>
                        </div>
                    </div>
                    <!-- Edit Doctor Modal -->
                    <div class="modal fade" id="editDoctorModal<?= $doc['staff_code'] ?>" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Edit Doctor Schedule</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <form method="POST" action="/admin/doctor/edit/<?= $doc['staff_code'] ?>">
                                        <?php
                                        $scheduleMap = [];
                                        if (!empty($doc['schedule_details'])) {
                                            foreach ($doc['schedule_details'] as $detail) {
                                                $scheduleMap[$detail['day_of_week']] = [
                                                    'start_time' => substr($detail['start_time'], 0, 5),
                                                    'end_time' => substr($detail['end_time'], 0, 5),
                                                ];
                                            }
                                        }
                                        $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
                                        ?>
                                        <div class="mb-3">
                                            <label class="form-label">Schedule</label>
                                            <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyMondayToAll(this)">Copy Monday to All</button>
                                            <?php foreach ($days as $day): ?>
                                                <?php 
                                                $active = isset($scheduleMap[$day]);
                                                $startTime = $active ? $scheduleMap[$day]['start_time'] : '';
                                                $endTime = $active ? $scheduleMap[$day]['end_time'] : '';
                                                ?>
                                                <div class="schedule-day mb-2<?= $active ? ' active' : '' ?>">
                                                    <div class="form-check">
                                                        <input class="form-check-input schedule-toggle" type="checkbox" 
                                                               name="schedule[<?= $day ?>][active]" 
                                                               id="edit-<?= $day ?>-<?= $doc['staff_code'] ?>" 
                                                               <?= $active ? 'checked' : '' ?> 
                                                               onchange="toggleTimeInputs(this)">
                                                        <label class="form-check-label" for="edit-<?= $day ?>-<?= $doc['staff_code'] ?>">
                                                            <?= $day ?>
                                                        </label>
                                                    </div>
                                                    <div class="row time-inputs" style="<?= $active ? '' : 'display:none;' ?>">
                                                        <div class="col">
                                                            <label class="form-label small">Start Time</label>
                                                            <input type="time" class="form-control" 
                                                                   name="schedule[<?= $day ?>][start_time]" 
                                                                   value="<?= $startTime ?>" 
                                                                   required>
                                                        </div>
                                                        <div class="col">
                                                            <label class="form-label small">End Time</label>
                                                            <input type="time" class="form-control" 
                                                                   name="schedule[<?= $day ?>][end_time]" 
                                                                   value="<?= $endTime ?>" 
                                                                   required>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        <div class="modal-footer">
                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                            <button type="submit" class="btn btn-primary">Update Schedule</button>
                                        </div>
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

    // Client-side search functionality (filters displayed cards)
    document.getElementById('searchDoctor').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase().trim();
        const doctorItems = document.querySelectorAll('.doctor-item');
        let visibleCount = 0;
        
        doctorItems.forEach(item => {
            const doctorName = item.getAttribute('data-name');
            const doctorPosition = item.getAttribute('data-position');
            
            if (doctorName.includes(searchValue) || doctorPosition.includes(searchValue)) {
                item.style.display = '';
                visibleCount++;
            } else {
                item.style.display = 'none';
            }
        });
        
        // Show or hide no results message
        let noResultsMsg = document.getElementById('noResultsMessage');
        if (visibleCount === 0 && searchValue !== '') {
            if (!noResultsMsg) {
                noResultsMsg = document.createElement('div');
                noResultsMsg.id = 'noResultsMessage';
                noResultsMsg.className = 'alert alert-info mt-3';
                noResultsMsg.innerHTML = `<i class="bi bi-info-circle"></i> No doctors found matching "${searchValue}". <a href="/admin/doctor">View all doctors</a>`;
                document.getElementById('doctorsContainer').parentNode.appendChild(noResultsMsg);
            }
        } else if (noResultsMsg) {
            noResultsMsg.remove();
        }
        
        // Update the doctor count
        document.querySelector('.doctor-count .badge').textContent = visibleCount;
    });

    function toggleTimeInputs(checkbox) {
        const scheduleDay = checkbox.closest('.schedule-day');
        const timeInputs = scheduleDay.querySelector('.time-inputs');
        const startTimeInput = timeInputs.querySelector('input[name*="[start_time]"]');
        const endTimeInput = timeInputs.querySelector('input[name*="[end_time]"]');
        if (checkbox.checked) {
            scheduleDay.classList.add('active');
            timeInputs.style.display = '';
            startTimeInput.required = true;
            endTimeInput.required = true;
        } else {
            scheduleDay.classList.remove('active');
            timeInputs.style.display = 'none';
            startTimeInput.required = false;
            endTimeInput.required = false;
            startTimeInput.value = '';
            endTimeInput.value = '';
        }
    }

    function copyMondayToAll(btn) {
        const modal = btn.closest('.modal');
        const mondayRow = modal.querySelector('.schedule-day .form-check-label[for^="edit-Monday-"], .schedule-day .form-check-label[for^="add-Monday"]').closest('.schedule-day');
        const mondayCheckbox = mondayRow.querySelector('input[type="checkbox"]');
        const mondayStart = mondayRow.querySelector('input[name*="[Monday]"][name*="[start_time]"]').value;
        const mondayEnd = mondayRow.querySelector('input[name*="[Monday]"][name*="[end_time]"]').value;
        if (!mondayStart || !mondayEnd) {
            alert('Please set Monday schedule first');
            return;
        }
        const days = ['Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
        days.forEach(day => {
            const dayRow = modal.querySelector('.schedule-day .form-check-label[for^="edit-' + day + '-"], .schedule-day .form-check-label[for^="add-' + day + '"]').closest('.schedule-day');
            const dayCheckbox = dayRow.querySelector('input[type="checkbox"]');
            dayCheckbox.checked = true;
            toggleTimeInputs(dayCheckbox);
            dayRow.querySelector('input[name*="['+day+']"][name*="[start_time]"]').value = mondayStart;
            dayRow.querySelector('input[name*="['+day+']"][name*="[end_time]"]').value = mondayEnd;
        });
    }

    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.schedule-toggle').forEach(function(checkbox) {
            toggleTimeInputs(checkbox);
        });
    });
</script>

</body>
</html>
