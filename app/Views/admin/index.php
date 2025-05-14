<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Preclinic Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
    
<?php include_once '../app/views/includes/navbar.php'; ?>

    <div class="d-flex">
    <?php include_once '../app/views/includes/sidebar.php'; ?>

        <div class="flex-grow-1 p-4">

        <h4 class="mb-4">Quick Stats</h4>
<div class="row row-cols-1 row-cols-md-4 g-4 mb-4">
    <div class="col">
        <div class="card text-white bg-primary h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-calendar-check me-2"></i>Today’s Appointments</h5>
                <p class="card-text fs-4">12</p> <!-- Replace 12 with dynamic PHP data -->
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-success h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-people me-2"></i>Total Clients</h5>
                <p class="card-text fs-4">86</p> <!-- Replace 86 with dynamic PHP data -->
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-warning h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-paw me-2"></i>Pets in System</h5>
                <p class="card-text fs-4">134</p> <!-- Replace 134 with dynamic PHP data -->
            </div>
        </div>
    </div>
    <div class="col">
        <div class="card text-white bg-danger h-100">
            <div class="card-body">
                <h5 class="card-title"><i class="bi bi-cash-coin me-2"></i>Revenue This Month</h5>
                <p class="card-text fs-4">₱45,000</p> <!-- Replace ₱45,000 with dynamic PHP data -->
            </div>
        </div>
    </div>
</div>

            <!-- Today's Appointments Table -->
<h4 class="mb-3 mt-5">Today’s Appointments</h4>
<div class="table-responsive mb-4">
    <table class="table table-bordered table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Pet Name</th>
                <th>Owner</th>
                <th>Time</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <!-- Example rows, replace with dynamic PHP data -->
            <tr>
                <td>Buddy</td>
                <td>Maria Lopez</td>
                <td>10:00 AM</td>
                <td><span class="badge bg-warning">Pending</span></td>
                <td>
                    <button class="btn btn-sm btn-success me-1">Approve</button>
                    <button class="btn btn-sm btn-secondary me-1">Reschedule</button>
                    <button class="btn btn-sm btn-danger">Cancel</button>
                </td>
            </tr>
            <tr>
                <td>Luna</td>
                <td>John Cruz</td>
                <td>11:30 AM</td>
                <td><span class="badge bg-success">Approved</span></td>
                <td>
                    <button class="btn btn-sm btn-success me-1">Approve</button>
                    <button class="btn btn-sm btn-secondary me-1">Reschedule</button>
                    <button class="btn btn-sm btn-danger">Cancel</button>
                </td>
            </tr>
            <!-- Add more rows dynamically -->
        </tbody>
    </table>
</div>


<!-- Quick Actions Buttons -->
<div class="d-flex justify-content-start flex-wrap gap-2 mb-5">
    <button class="btn btn-outline-primary"><i class="bi bi-calendar-plus me-1"></i> Add Appointment</button>
    <button class="btn btn-outline-success"><i class="bi bi-plus-circle me-1"></i> Register New Pet</button>
    <button class="btn btn-outline-warning"><i class="bi bi-credit-card me-1"></i> Add Payment</button>
    <button class="btn btn-outline-info"><i class="bi bi-gear me-1"></i> Add Service</button>
</div>

<!-- Notifications Box -->
<div class="position-fixed bottom-0 end-0 m-4" style="z-index: 1030; width: 300px;">
    <div class="card shadow">
        <div class="card-header bg-danger text-white">
            <i class="bi bi-bell-fill me-2"></i>Notifications / Reminders
        </div>
        <div class="card-body small">
            <ul class="mb-0">
                <li>2 unpaid bills</li>
                <li>1 appointment needs confirmation</li>
                <li>3 pet vaccinations due next week</li>
            </ul>
        </div>
    </div>
</div>

        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
