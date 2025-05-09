<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Veterinary Doctors</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        /* Reset margins, padding, and ensure background is white */
        html, body {
            margin: 0;
            padding: 0;
            height: 100%;
            font-family: Arial, sans-serif;
            background-color: #ffffff; /* Set background to white */
            overflow-x: hidden; /* Prevent horizontal scrolling */
        }

        /* Full height for container */
        .container-fluid {
            padding-left: 0;
            padding-right: 0;
            height: 100%;
        }

        /* Sidebar and content spacing */
        .row {
            height: 100%;
        }

        /* Ensure the card background is fully white */
        .doctor-card {
            background-color: #fff;
            padding: 20px;
            margin-bottom: 15px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        }

        /* Style for schedule block */
        .schedule {
            background-color: #fff;
            padding: 10px;
            border-radius: 4px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        h1, h2 {
            color: #333;
        }

        p {
            font-size: 1.1em;
            color: #555;
        }

        /* Adjust the sidebar to fill the full height */
        .col-md-3, .col-lg-2 {
            height: 100vh;
            position: sticky;
            top: 0;
            overflow-y: auto;
        }

        /* Make the main content flexible */
        .col-md-9, .col-lg-10 {
            height: 100%;
            padding-top: 20px;
        }
    </style>
</head>
<body>

<!-- Include the navbar and sidebar (outside the doctor loop) -->
<?php include_once '../app/views/includes/navbar.php'; ?>
<?php include_once '../app/views/includes/sidebar.php'; ?>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 bg-light p-3">
            <?php include_once '../app/views/includes/sidebar.php'; ?>
        </div>

        <!-- Main content -->
        <div class="col-md-9 col-lg-10 p-4">
            <h1>Veterinary Doctors & Schedules</h1>

            <!-- Loop through each doctor and display their information -->
            <?php foreach ($doctors as $doc): ?>
                <div class="doctor-card">
                    <h2><?= htmlspecialchars($doc['staff_name']) ?></h2>
                    <p><strong>Position:</strong> <?= htmlspecialchars($doc['staff_position']) ?></p>
                    <p><strong>Contact:</strong> <?= htmlspecialchars($doc['staff_contact']) ?></p>
                    <p><strong>Email:</strong> <?= htmlspecialchars($doc['staff_email_address']) ?></p>

                    <div class="schedule">
                        <strong>Schedule:</strong>
                        <pre><?= htmlspecialchars($doc['staff_schedule']) ?></pre> <!-- Ensure it's readable -->
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
