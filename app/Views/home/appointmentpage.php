<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get form data
    $name = $_POST['name'];
    $contact_number = $_POST['contact-number'];
    $email = $_POST['email'];
    $pet_name = $_POST['pet-name'];
    $pet_type = $_POST['pet-type'];
    $service = $_POST['service'];
    $appointment_time = $_POST['appointment-time'];
    $notes = $_POST['notes'];
    $reminder = isset($_POST['reminder']) ? 'Yes' : 'No';

    // Send email confirmation (basic email setup, adjust for your server)
    $to = $email;
    $subject = "Appointment Confirmation - MaVetCare";
    $message = "
    Hello $name,
    
    Your appointment has been successfully booked for $pet_name ($pet_type).
    
    Service: $service
    Preferred Date & Time: $appointment_time
    Additional Notes: $notes
    Reminder: $reminder
    
    Thank you for choosing MaVetCare! We will send you a reminder closer to the date.
    
    Best Regards,
    MaVetCare Team
    ";

    $headers = "From: no-reply@mavetcare.com";

    mail($to, $subject, $message, $headers);

    // Confirmation message
    $confirmation_message = "Your appointment has been successfully booked! An email confirmation has been sent to $email.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment - MaVetCare</title>
    <link rel="stylesheet" href="styles.css"> <!-- Link to your CSS -->
</head>
<body>
    <!-- Header Section (Same as Home Page) -->
    <header>
        <nav>
            <!-- Navigation Bar or Logo Here -->
        </nav>
    </header>

    <!-- Appointment Booking Section -->
    <section id="appointment-section">
        <h1>Book an Appointment</h1>

        <?php if (isset($confirmation_message)): ?>
            <div id="confirmation-message">
                <p><?php echo $confirmation_message; ?></p>
            </div>
        <?php endif; ?>

        <form id="appointment-form" method="POST" action="appointment.php">
            <!-- Name -->
            <label for="name">Full Name:</label>
            <input type="text" id="name" name="name" required>

            <!-- Contact Number -->
            <label for="contact-number">Contact Number:</label>
            <input type="tel" id="contact-number" name="contact-number" required>

            <!-- Email -->
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>

            <!-- Pet Name & Type -->
            <label for="pet-name">Pet Name:</label>
            <input type="text" id="pet-name" name="pet-name" required>
            
            <label for="pet-type">Pet Type:</label>
            <select id="pet-type" name="pet-type" required>
                <option value="dog">Dog</option>
                <option value="cat">Cat</option>
                <option value="other">Other</option>
            </select>

            <!-- Service Selection -->
            <label for="service">Select Service:</label>
            <select id="service" name="service" required>
                <option value="vaccination">Vaccination</option>
                <option value="grooming">Grooming</option>
                <option value="surgery">Surgery</option>
                <!-- Add other services as needed -->
            </select>

            <!-- Preferred Date & Time -->
            <label for="appointment-time">Preferred Date & Time:</label>
            <input type="datetime-local" id="appointment-time" name="appointment-time" required>

            <!-- Additional Notes -->
            <label for="notes">Additional Notes:</label>
            <textarea id="notes" name="notes"></textarea>

            <!-- Reminder Option -->
            <label for="reminder">Receive appointment reminder via SMS/email</label>
            <input type="checkbox" id="reminder" name="reminder">

            <!-- Submit Button -->
            <button type="submit">Book Appointment</button>
        </form>
    </section>

    <!-- FAQs Section (Optional) -->
    <section id="faqs">
        <h2>FAQs</h2>
        <ul>
            <li><strong>How do I reschedule?</strong> Contact us via phone or email to reschedule your appointment.</li>
            <li><strong>What are your clinic hours?</strong> Our clinic is open Monday to Saturday, from 9 AM to 5 PM.</li>
        </ul>
    </section>

    <!-- Footer Section (Same as Home Page) -->
    <footer>
        <p>&copy; 2025 MaVetCare | All Rights Reserved</p>
    </footer>
</body>
</html>
