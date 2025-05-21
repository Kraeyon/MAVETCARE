<?php
require_once '../config/database.php';
use Config\Database;

$pdo = Database::getInstance()->getConnection();

// Fetch all staff for display and for copy schedule dropdown
$stmt = $pdo->query("SELECT staff_code, staff_name FROM veterinary_staff ORDER BY staff_name ASC");
$allStaff = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle staff addition
if (isset($_POST['add_staff'])) {
    $staff_name = $_POST['staff_name'];
    $staff_position = $_POST['staff_position'];
    $staff_contact = $_POST['staff_contact'];
    $staff_email = $_POST['staff_email'];
    
    try {
        $pdo->beginTransaction();
        
        // Insert staff member
        $stmt = $pdo->prepare("INSERT INTO veterinary_staff (staff_name, staff_position, staff_contact, staff_email_address) VALUES (?, ?, ?, ?)");
        $stmt->execute([$staff_name, $staff_position, $staff_contact, $staff_email]);
        $staff_code = $pdo->lastInsertId();
        
        // Insert schedule
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            if (isset($_POST['schedule'][$day]['active'])) {
                $start_time = $_POST['schedule'][$day]['start_time'];
                $end_time = $_POST['schedule'][$day]['end_time'];
                
                $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                $stmt->execute([$staff_code, $day, $start_time, $end_time]);
            }
        }
        
        $pdo->commit();
        header("Location: employees.php?added=1");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Database error: " . $e->getMessage());
    }
}

// Handle staff update
if (isset($_POST['update_staff'])) {
    $staff_code = $_POST['staff_code'];
    $staff_name = $_POST['staff_name'];
    $staff_position = $_POST['staff_position'];
    $staff_contact = $_POST['staff_contact'];
    $staff_email = $_POST['staff_email'];
    
    try {
        $pdo->beginTransaction();
        
        // Update staff member
        $stmt = $pdo->prepare("UPDATE veterinary_staff SET staff_name = ?, staff_position = ?, staff_contact = ?, staff_email_address = ? WHERE staff_code = ?");
        $stmt->execute([$staff_name, $staff_position, $staff_contact, $staff_email, $staff_code]);
        
        // Delete existing schedule
        $stmt = $pdo->prepare("DELETE FROM staff_schedule WHERE staff_code = ?");
        $stmt->execute([$staff_code]);
        
        // Insert new schedule
        $days = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
        foreach ($days as $day) {
            if (isset($_POST['schedule'][$day]['active'])) {
                $start_time = $_POST['schedule'][$day]['start_time'];
                $end_time = $_POST['schedule'][$day]['end_time'];
                
                $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                $stmt->execute([$staff_code, $day, $start_time, $end_time]);
            }
        }
        
        $pdo->commit();
        header("Location: employees.php?updated=1");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Database error: " . $e->getMessage());
    }
}

// Handle staff deletion
if (isset($_GET['delete'])) {
    $staff_code = $_GET['delete'];
    try {
        $pdo->beginTransaction();
        
        // Delete schedule first (due to foreign key constraint)
        $stmt = $pdo->prepare("DELETE FROM staff_schedule WHERE staff_code = ?");
        $stmt->execute([$staff_code]);
        
        // Delete staff member
        $stmt = $pdo->prepare("DELETE FROM veterinary_staff WHERE staff_code = ?");
        $stmt->execute([$staff_code]);
        
        $pdo->commit();
        header("Location: employees.php?deleted=1");
        exit;
    } catch (PDOException $e) {
        $pdo->rollBack();
        die("Database error: " . $e->getMessage());
    }
}

// Get all staff members
$stmt = $pdo->query("SELECT * FROM veterinary_staff ORDER BY staff_name ASC");
$staff = $stmt->fetchAll(PDO::FETCH_ASSOC);

// For each staff, fetch their schedule as an array of rows
foreach ($staff as &$member) {
    $stmt2 = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM staff_schedule WHERE staff_code = ?");
    $stmt2->execute([$member['staff_code']]);
    $member['schedule_details'] = $stmt2->fetchAll(PDO::FETCH_ASSOC);
}
unset($member);

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

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="icon" type="image/png" href="/assets/images/paw.png">
    <title>Staff Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
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
        .schedule-day {
            border: 1px solid #dee2e6;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 5px;
            transition: all 0.3s ease;
        }
        .schedule-day.active {
            background-color: #e9ecef;
            border-color: #0d6efd;
        }
        .schedule-day .form-check {
            margin-bottom: 10px;
        }
        .schedule-day .time-inputs {
            display: none;
            padding-top: 10px;
            border-top: 1px solid #dee2e6;
        }
        .schedule-day.active .time-inputs {
            display: block;
        }
        .schedule-day:hover {
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .schedule-day.active:hover {
            box-shadow: 0 2px 4px rgba(13,110,253,0.2);
        }
    </style>
</head>
<body>

<?php include_once '../app/views/includes/navbar.php'; ?>

<div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>

    <div class="flex-grow-1 p-4">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Staff Management</h2>
            <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addStaffModal">
                <i class="bi bi-plus-circle"></i> Add New Staff
            </button>
        </div>

        <?php if (isset($_GET['added'])): ?>
            <div class="alert alert-success">Staff member added successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['updated'])): ?>
            <div class="alert alert-success">Staff member updated successfully!</div>
        <?php endif; ?>

        <?php if (isset($_GET['deleted'])): ?>
            <div class="alert alert-success">Staff member deleted successfully!</div>
        <?php endif; ?>

        <!-- Staff Table with Inline Editing -->
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">Veterinary Staff</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover" id="staffTable">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Position</th>
                                <th>Contact</th>
                                <th>Email</th>
                                <th>Schedule</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($staff as $member): ?>
                                <tr>
                                    <td><?= htmlspecialchars($member['staff_name']) ?></td>
                                    <td><?= htmlspecialchars($member['staff_position']) ?></td>
                                    <td><?= htmlspecialchars($member['staff_contact']) ?></td>
                                    <td><?= htmlspecialchars($member['staff_email_address']) ?></td>
                                    <td>
                                        <div class="schedule-container mt-3">
                                            <p class="text-muted mb-1"><i class="bi bi-calendar-week"></i> Schedule:</p>
                                            <div class="schedule-display">
                                                <?= formatSchedule($member['schedule_details']) ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <button class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#editStaffModal<?= $member['staff_code'] ?>">
                                            <i class="bi bi-pencil"></i>
                                        </button>
                                        <form method="POST" action="/admin/employees/delete" class="d-inline delete-form">
                                            <input type="hidden" name="staff_code" value="<?= $member['staff_code'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this staff member?')">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                                <!-- Edit Staff Modal -->
                                <div class="modal fade" id="editStaffModal<?= $member['staff_code'] ?>" tabindex="-1">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title">Edit Staff Member</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form method="POST" action="/admin/employees/edit">
                                                    <input type="hidden" name="staff_code" value="<?= $member['staff_code'] ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label">Name</label>
                                                        <input type="text" class="form-control" name="staff_name" value="<?= htmlspecialchars($member['staff_name']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Position</label>
                                                        <select class="form-select" name="staff_position" required>
                                                            <option value="Doctor" <?= $member['staff_position'] == 'Doctor' ? 'selected' : '' ?>>Doctor</option>
                                                            <option value="Veterinary Technician" <?= $member['staff_position'] == 'Veterinary Technician' ? 'selected' : '' ?>>Veterinary Technician</option>
                                                            <option value="Receptionist" <?= $member['staff_position'] == 'Receptionist' ? 'selected' : '' ?>>Receptionist</option>
                                                            <option value="Groomer" <?= $member['staff_position'] == 'Groomer' ? 'selected' : '' ?>>Groomer</option>
                                                            <option value="Assistant" <?= $member['staff_position'] == 'Assistant' ? 'selected' : '' ?>>Assistant</option>
                                                        </select>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Contact Number</label>
                                                        <input type="tel" class="form-control" name="staff_contact" value="<?= htmlspecialchars($member['staff_contact']) ?>" required>
                                                    </div>
                                                    <div class="mb-3">
                                                        <label class="form-label">Email</label>
                                                        <input type="email" class="form-control" name="staff_email" value="<?= htmlspecialchars($member['staff_email_address']) ?>" required>
                                                    </div>
                                                    <?php
                                                    // Prepare schedule map for this staff member
                                                    $scheduleMap = [];
                                                    if (!empty($member['schedule_details'])) {
                                                        foreach ($member['schedule_details'] as $detail) {
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
                                                                           id="edit-<?= $day ?>-<?= $member['staff_code'] ?>" 
                                                                           <?= $active ? 'checked' : '' ?> 
                                                                           onchange="toggleTimeInputs(this)">
                                                                    <label class="form-check-label" for="edit-<?= $day ?>-<?= $member['staff_code'] ?>">
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
                                                        <button type="submit" class="btn btn-primary">Update Staff</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add New Staff Button and Modal -->
<div class="modal fade" id="addStaffModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Staff Member</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form method="POST" action="/admin/employees/add">
                    <div class="mb-3">
                        <label class="form-label">Name</label>
                        <input type="text" class="form-control" name="staff_name" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <select class="form-select" name="staff_position" required>
                            <option value="Doctor">Doctor</option>
                            <option value="Veterinary Technician">Veterinary Technician</option>
                            <option value="Receptionist">Receptionist</option>
                            <option value="Groomer">Groomer</option>
                            <option value="Assistant">Assistant</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Contact Number</label>
                        <input type="tel" class="form-control" name="staff_contact" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="staff_email" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Schedule</label>
                        <button type="button" class="btn btn-sm btn-outline-primary ms-2" onclick="copyMondayToAll(this)">Copy Monday to All</button>
                        <?php $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday']; ?>
                        <?php foreach ($days as $day): ?>
                            <div class="schedule-day mb-2">
                                <div class="form-check">
                                    <input class="form-check-input schedule-toggle" type="checkbox" name="schedule[<?= $day ?>][active]" id="add-<?= $day ?>">
                                    <label class="form-check-label" for="add-<?= $day ?>"> <?= $day ?> </label>
                                </div>
                                <div class="row time-inputs" style="display:none;">
                                    <div class="col">
                                        <label class="form-label small">Start Time</label>
                                        <input type="time" class="form-control" name="schedule[<?= $day ?>][start_time]" placeholder="Start Time">
                                    </div>
                                    <div class="col">
                                        <label class="form-label small">End Time</label>
                                        <input type="time" class="form-control" name="schedule[<?= $day ?>][end_time]" placeholder="End Time">
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-primary">Add Staff</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Inline editing logic
const staffTable = document.getElementById('staffTable');
let editingRow = null;

staffTable.addEventListener('click', function(e) {
    const target = e.target.closest('button');
    if (!target) return;
    const row = target.closest('tr');
    if (target.classList.contains('edit-btn')) {
        if (editingRow) return; // Only one at a time
        editingRow = row;
        row.querySelectorAll('.editable').forEach(td => {
            const field = td.dataset.field;
            const value = td.textContent.trim();
            if (field === 'staff_position') {
                td.innerHTML = `<select class="form-select form-select-sm">`
                    + `<option value="Doctor"${value==='Doctor'?' selected':''}>Doctor</option>`
                    + `<option value="Veterinary Technician"${value==='Veterinary Technician'?' selected':''}>Veterinary Technician</option>`
                    + `<option value="Receptionist"${value==='Receptionist'?' selected':''}>Receptionist</option>`
                    + `<option value="Groomer"${value==='Groomer'?' selected':''}>Groomer</option>`
                    + `<option value="Assistant"${value==='Assistant'?' selected':''}>Assistant</option>`
                    + `</select>`;
            } else {
                td.innerHTML = `<input type="text" class="form-control form-control-sm" value="${value}">`;
            }
        });
        // Schedule editing (optional: could open a modal or inline form)
        row.querySelector('.editable-schedule').innerHTML = '<span class="text-muted">Edit schedule in modal or use copy feature.</span>';
        row.querySelector('.edit-btn').classList.add('d-none');
        row.querySelector('.save-btn').classList.remove('d-none');
        row.querySelector('.cancel-btn').classList.remove('d-none');
    } else if (target.classList.contains('cancel-btn')) {
        window.location.reload();
    } else if (target.classList.contains('save-btn')) {
        // Gather data
        const staff_code = row.dataset.staffCode;
        const tds = row.querySelectorAll('.editable');
        const data = {
            staff_code: staff_code,
            staff_name: tds[0].querySelector('input') ? tds[0].querySelector('input').value : tds[0].textContent.trim(),
            staff_position: tds[1].querySelector('select') ? tds[1].querySelector('select').value : tds[1].textContent.trim(),
            staff_contact: tds[2].querySelector('input') ? tds[2].querySelector('input').value : tds[2].textContent.trim(),
            staff_email: tds[3].querySelector('input') ? tds[3].querySelector('input').value : tds[3].textContent.trim(),
        };
        // AJAX POST to /admin/employees/edit
        fetch('/admin/employees/edit', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams(data)
        }).then(res => {
            if (res.ok) window.location.reload();
            else alert('Failed to save changes.');
        });
    }
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

// Initialize all schedule toggles
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.schedule-toggle').forEach(function(checkbox) {
        toggleTimeInputs(checkbox);
    });
});
</script>
</body>
</html> 