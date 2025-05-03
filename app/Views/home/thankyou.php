<?php 
// Start session to check for success
session_start();

// Check if the appointment was successful and the appointment ID is stored
if (!isset($_SESSION['appointment_success']) || !isset($_SESSION['appointment_id'])) {
    header("Location: appointment.php"); // Redirect back to appointment page if session is invalid
    exit();
}

// Get appointment details
$appointment_id = $_SESSION['appointment_id'];

// Clear session variables after use to prevent re-use on page refresh
unset($_SESSION['appointment_success']);
unset($_SESSION['appointment_id']);

// Optional: You can retrieve the details from the database if needed using the appointment ID
// Here, we'll just display a sample confirmation message.
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thank You - MaVetCare</title>
    <link rel="stylesheet" href="../assets/css/style.css"> <!-- Include your CSS file -->
</head>
<body>
    <?php include '../app/views/includes/header.php'; ?> <!-- Optional: include your header -->

    <section class="thank-you-section">
        <div class="container">
            <h1>Thank You for Your Appointment!</h1>
            <p>Your appointment has been successfully booked. Below are your appointment details:</p>
            
            <!-- Appointment Details -->
            <div class="appointment-details">
                <ul>
                    <li><strong>Appointment ID:</strong> #<?php echo $appointment_id; ?></li>
                    <!-- You can also fetch more details like owner name, pet name, etc., from the database if needed -->
                    <li><strong>Pet Name:</strong> [Pet Name]</li>
                    <li><strong>Service:</strong> [Service Type]</li>
                    <li><strong>Date & Time:</strong> [Preferred Date & Time]</li>
                </ul>
            </div>

            <p>We will contact you shortly to confirm your appointment. In case of any changes or if you need assistance, feel free to reach out to us.</p>
            
            <a href="appointment.php" class="btn btn-primary">Book Another Appointment</a> <!-- Link to book another appointment -->

            <a href="index.php" class="btn btn-secondary">Return to Homepage</a> <!-- Link to homepage -->
        </div>
    </section>

    <?php include '../app/views/includes/footer.php'; ?> <!-- Optional: include your footer -->
</body>
</html>
