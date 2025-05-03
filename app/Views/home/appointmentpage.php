<?php include '../includes/header.php'; ?>

<?php
/**
 * MaVetCare Appointment Booking Form Handler (Optimized)
 */

if (session_status() == PHP_SESSION_NONE) session_start();

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'mavetcare_admin');
define('DB_PASS', 'YOUR_PASSWORD_HERE');  // Change this in production
define('DB_NAME', 'mavetcare_appointments');

// Initialize
$errors = [];
$success = false;
$conn = null;

// Simple database connection function (DRY)
function getDBConnection() {
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $conn;
    } catch(PDOException $e) {
        die("Database connection failed: " . $e->getMessage());
    }
}

// Validate and sanitize helper
function sanitize($field, $type = 'string') {
    switch ($type) {
        case 'email': return filter_input(INPUT_POST, $field, FILTER_SANITIZE_EMAIL);
        case 'string': default: return filter_input(INPUT_POST, $field, FILTER_SANITIZE_STRING);
    }
}

// Process form
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $owner_name = sanitize('owner_name');
    $contact_number = sanitize('contact_number');
    $email = sanitize('email', 'email');
    $pet_name = sanitize('pet_name');
    $pet_type = sanitize('pet_type');
    $service = sanitize('service');
    $preferred_date = sanitize('preferred_date');
    $preferred_time = sanitize('preferred_time');
    $appointment_type = sanitize('appointment_type');
    $additional_notes = sanitize('additional_notes');

    // Validation
    if (!$owner_name) $errors[] = "Owner's name is required.";
    if (!$contact_number) $errors[] = "Contact number is required.";
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "A valid email is required.";
    if (!$pet_name) $errors[] = "Pet's name is required.";
    if (!$pet_type) $errors[] = "Pet type is required.";
    if (!$service) $errors[] = "Service is required.";
    if (!$preferred_date) $errors[] = "Preferred date is required.";
    if (!$preferred_time) $errors[] = "Preferred time is required.";
    if (!$appointment_type) $errors[] = "Appointment type is required.";

    // Proceed if valid
    if (empty($errors)) {
        $conn = getDBConnection();

        // Check availability first (server-side)
        $stmt = $conn->prepare("SELECT COUNT(*) FROM appointments WHERE preferred_date = :date AND preferred_time = :time");
        $stmt->execute([':date' => $preferred_date, ':time' => $preferred_time]);
        $count = $stmt->fetchColumn();

        $max_appointments = 3;
        if ($count >= $max_appointments) {
            $errors[] = "Sorry, the selected time slot is already full. Please choose another.";
        } else {
            // Save appointment
            $stmt = $conn->prepare("INSERT INTO appointments 
                (owner_name, contact_number, email, pet_name, pet_type, service, preferred_date, preferred_time, appointment_type, additional_notes, created_at) 
                VALUES (:owner_name, :contact_number, :email, :pet_name, :pet_type, :service, :preferred_date, :preferred_time, :appointment_type, :additional_notes, NOW())");
            
            $stmt->execute([
                ':owner_name' => $owner_name,
                ':contact_number' => $contact_number,
                ':email' => $email,
                ':pet_name' => $pet_name,
                ':pet_type' => $pet_type,
                ':service' => $service,
                ':preferred_date' => $preferred_date,
                ':preferred_time' => $preferred_time,
                ':appointment_type' => $appointment_type,
                ':additional_notes' => $additional_notes
            ]);

            $appointment_id = $conn->lastInsertId();

            // Send confirmation email (client + admin)
            function sendEmail($to, $subject, $message) {
                $headers = "MIME-Version: 1.0\r\n";
                $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                $headers .= "From: appointments@mavetcare.com\r\n";
                mail($to, $subject, $message, $headers);
            }

            $user_subject = "MaVetCare Appointment Confirmation";
            $user_message = "
                <h2>Thank you, $owner_name!</h2>
                <p>Your appointment has been booked successfully. Details below:</p>
                <ul>
                    <li><strong>Appointment ID:</strong> #$appointment_id</li>
                    <li><strong>Pet Name:</strong> $pet_name</li>
                    <li><strong>Service:</strong> $service</li>
                    <li><strong>Date & Time:</strong> $preferred_date at $preferred_time</li>
                </ul>
                <p>We will contact you to confirm shortly.</p>
            ";

            $admin_subject = "New Appointment Booking - #$appointment_id";
            $admin_message = "
                <h2>New Appointment</h2>
                <ul>
                    <li><strong>Owner:</strong> $owner_name</li>
                    <li><strong>Contact:</strong> $contact_number</li>
                    <li><strong>Pet:</strong> $pet_name ($pet_type)</li>
                    <li><strong>Service:</strong> $service</li>
                    <li><strong>Date & Time:</strong> $preferred_date at $preferred_time</li>
                    <li><strong>Notes:</strong> $additional_notes</li>
                </ul>
            ";

            sendEmail($email, $user_subject, $user_message);
            sendEmail("mavetcare@email.com", $admin_subject, $admin_message);

            // Redirect
            $_SESSION['appointment_success'] = true;
            $_SESSION['appointment_id'] = $appointment_id;
            header("Location: thank-you.php");
            exit();
        }
    }
}

// Reuse functions
function getAvailableServices() {
    $default_services = ['Wellness Check-up', 'Vaccination', 'Dental Cleaning', 'Spay/Neuter', 'Microchipping', 'Grooming', 'Surgery Consultation', 'Emergency Care'];
    try {
        $conn = getDBConnection();
        $stmt = $conn->query("SELECT service_name FROM services WHERE active = 1 ORDER BY service_name");
        return $stmt->fetchAll(PDO::FETCH_COLUMN) ?: $default_services;
    } catch (Exception $e) {
        return $default_services;
    }
}

function getPetTypes() {
    return ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster/Guinea Pig', 'Reptile', 'Fish', 'Other'];
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment - MaVetCare</title>
    <link rel="stylesheet" href="../assets/css/appointmentpage.css"> <!-- Link to your CSS -->
</head>
<body>
    <!-- Header Section (Same as Home Page) -->
    
<?php include_once '../app/views/includes/header.php'; ?>

    <section class="py-5 bg-light text-center">
    <div class="container">
        <div class="row align-items-center">

        <!-- Text Content -->
        <div class="col-md-6 mb-4 mb-md-0 text-md-start text-center">
            <h1 class="display-5 fw-bold mb-3">
            <span class="d-block text-primary fs-3 fw-semibold mb-2 fade-in-up" style="animation-delay: 0.2s;">
                Welcome to MaVetCare!
            </span>
            <span class="fade-in-up" style="animation-delay: 0.4s;">
                Book Your Pet’s Appointment
            </span>
            </h1>

            <p class="lead fade-in-up" style="animation-delay: 0.6s;">
            Quick, Easy, Hassle-Free. Secure your slot and get the care your pet deserves — all in just a few clicks.
            </p>

            <a href="#appointment-section" class="btn btn-primary mt-3 fade-in-up" style="animation-delay: 0.8s;">
            Book Now!
            </a>
        </div>

        <!-- Image -->
        <div class="col-md-6">
            <img src="../assets/images/services_dog&cat.png" class="img-fluid rounded fade-in-up" alt="Pet Appointment" style="animation-delay: 1s;">
        </div>

        </div>
    </div>
    </section>

    <!-- Appointment Booking Section -->
    <section id="appointment-section">
        <div class="appointment-header">
            <span class="paw-icon">🐾</span>
            <h1>Appointment Booking Form</h1>
            <span class="paw-icon">🐾</span>
        </div>


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
                <option value="deworming">Deworming</option>
                <option value="anti-parasitic program">Anti-parasitic Program</option>
                <option value="surgery">Surgery</option>
                <option value="grooming">Grooming</option>
                <option value="treatment">Treatment</option>
                <option value="confinement">Confinement</option>

                <!-- Add other services as needed -->
            </select>
            <!-- Type of Appointment -->
                <label for="appointment-type">Type of Appointment:</label>
                <select id="appointment-type" name="appointment-type" required>
                    <option value="walk-in">Walk-in</option>
                    <option value="service-on-call">Service-on-call</option>
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
    <?php include_once '../app/views/includes/footer.php'; ?>
</body>
</html>
