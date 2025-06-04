<?php include '../includes/header.php'; ?>

<?php
/**
 * MaVetCare Appointment Booking Form
 */

// Only keep session handling for the thank you modal
if (session_status() == PHP_SESSION_NONE) session_start();

// Debug session variables for appointment success
error_log("APPOINTMENT PAGE SESSION CHECK: appointment_success=" . 
    (isset($_SESSION['appointment_success']) ? $_SESSION['appointment_success'] : 'not set') . 
    ", appointment_id=" . (isset($_SESSION['appointment_id']) ? $_SESSION['appointment_id'] : 'not set'));

$showThankYou = false;

if (isset($_SESSION['appointment_success']) && $_SESSION['appointment_success'] === true) {
    $showThankYou = true;
    $appointment_id = $_SESSION['appointment_id'];
    error_log("SHOWING THANK YOU MODAL: true, appointment_id=" . $appointment_id);

    // Clear session variable so popup doesn't show again on refresh
    unset($_SESSION['appointment_success']);
    unset($_SESSION['appointment_id']);
} else {
    error_log("SHOWING THANK YOU MODAL: false, condition not met");
}

// Today's date for date input min attribute
$today = date('Y-m-d');

// Pet types for dropdown
function getPetTypes() {
    return ['Dog', 'Cat', 'Bird', 'Rabbit', 'Hamster/Guinea Pig', 'Reptile', 'Fish', 'Other'];
}

// Services for dropdown
function getAvailableServices() {
    $default_services = ['Wellness Check-up', 'Vaccination', 'Dental Cleaning', 'Spay/Neuter', 'Microchipping', 'Grooming', 'Surgery Consultation', 'Emergency Care'];
    return $default_services;
}

// Get user data
$user = $user ?? null;
$pets = $pets ?? [];
$errors = $errors ?? [];
$form_data = $form_data ?? [];

// Determine which tab should be active based on form data or errors
$activeTab = 'existing-pet';
if (!empty($form_data) && isset($form_data['form_type']) && $form_data['form_type'] === 'new_pet') {
    $activeTab = 'new-pet';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Appointment - MaVetCare</title>
    <link rel="stylesheet" href="../assets/css/appointmentpage.css"> <!-- Link to your CSS -->
    <style>
        /* Modern styling for the appointment page */
        #appointment-section {
            max-width: 900px;
            margin: 0 auto 60px;
            padding: 30px;
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,0.08);
        }
        
        .appointment-header {
            text-align: center;
            margin-bottom: 30px;
            position: relative;
        }
        
        .appointment-header h1 {
            font-size: 2rem;
            color: #333;
            margin: 0 15px;
            display: inline-block;
            font-weight: 600;
        }
        
        .paw-icon {
            font-size: 1.5rem;
            color: #3183FF;
            vertical-align: middle;
        }
        
        /* Tabs styling */
        .tab {
            display: none;
            padding: 25px;
            background-color: #f9fafb;
            border-radius: 10px;
            border: 1px solid #eaedf1;
        }
        
        .tab.active {
            display: block;
            animation: fadeIn 0.5s;
        }
        
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        
        .tab-buttons {
            display: flex;
            margin-bottom: 20px;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .tab-button {
            padding: 15px 20px;
            background-color: #f0f3f7;
            border: none;
            cursor: pointer;
            flex: 1;
            text-align: center;
            transition: all 0.3s;
            font-weight: 500;
            color: #5e6e82;
            position: relative;
        }
        
        .tab-button:hover {
            background-color: #e6ebf5;
            color: #3183FF;
        }
        
        .tab-button:first-child {
            border-radius: 10px 0 0 10px;
        }
        
        .tab-button:last-child {
            border-radius: 0 10px 10px 0;
        }
        
        .tab-button.active {
            background-color: #3183FF;
            color: white;
            box-shadow: 0 4px 15px rgba(49, 131, 255, 0.3);
        }
        
        .tab-button.active::after {
            content: '';
            position: absolute;
            bottom: -8px;
            left: 50%;
            transform: translateX(-50%);
            border-left: 8px solid transparent;
            border-right: 8px solid transparent;
            border-top: 8px solid #3183FF;
        }
        
        /* Form styling */
        form {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }
        
        @media (min-width: 768px) {
            form {
                grid-template-columns: 1fr 1fr;
                gap: 25px 30px;
            }
            
            form .full-width {
                grid-column: span 2;
            }
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: #4d5b75;
            font-size: 0.95rem;
        }
        
        select, input[type="date"], input[type="text"], input[type="number"], textarea {
            width: 100%;
            padding: 12px 15px;
            border: 1px solid #dce0ea;
            border-radius: 8px;
            background-color: #fff;
            font-size: 1rem;
            transition: all 0.3s;
            color: #3c4257;
            box-shadow: 0 2px 5px rgba(0,0,0,0.02);
        }
        
        select:focus, input:focus, textarea:focus {
            outline: none;
            border-color: #3183FF;
            box-shadow: 0 0 0 3px rgba(49, 131, 255, 0.15);
        }
        
        select {
            appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='24' height='24' viewBox='0 0 24 24' fill='none' stroke='%23516178' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 12px center;
            background-size: 16px;
        }
        
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        
        /* Checkbox styling */
        .checkbox-container {
            display: flex;
            align-items: center;
            margin-top: 5px;
        }
        
        .checkbox-container input[type="checkbox"] {
            width: auto;
            margin-right: 10px;
            accent-color: #3183FF;
            transform: scale(1.2);
        }
        
        .checkbox-container label {
            margin-bottom: 0;
            cursor: pointer;
        }
        
        /* Button styling */
        #appointment-section button[type="submit"] {
            background-color: #3183FF;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 14px 25px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            box-shadow: 0 4px 15px rgba(49, 131, 255, 0.25);
            margin-top: 10px;
            width: 100%;
        }
        
        #appointment-section button[type="submit"]:hover {
            background-color: #2468d9;
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(49, 131, 255, 0.35);
        }
        
        /* Section divider */
        hr {
            margin: 30px 0;
            border: none;
            height: 1px;
            background-image: linear-gradient(to right, rgba(0, 0, 0, 0), rgba(49, 131, 255, 0.2), rgba(0, 0, 0, 0));
        }
        
        /* Error message styling */
        .error-message {
            color: #e74c3c;
            background-color: #fdf0ed;
            border-left: 4px solid #e74c3c;
            border-radius: 5px;
            padding: 15px;
            margin-bottom: 25px;
            font-size: 0.95rem;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        }
        
        .error-message ul {
            margin: 8px 0 0 20px;
            padding: 0;
        }
        
        .error-message li {
            margin-bottom: 5px;
        }
        
        /* Alert styling */
        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }
        
        .alert-icon {
            margin-right: 12px;
            flex-shrink: 0;
        }
        
        .alert-info {
            background-color: #e1f0ff;
            border-left: 4px solid #3183FF;
            color: #2468d9;
        }
        
        /* FAQ section styling */
        #faqs {
            max-width: 900px;
            margin: 0 auto 80px;
            padding: 0 20px;
        }
        
        #faqs h2 {
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8rem;
            position: relative;
            padding-bottom: 10px;
        }
        
        #faqs h2:after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            width: 60px;
            height: 3px;
            background-color: #3183FF;
        }
        
        #faqs ul {
            list-style: none;
            padding: 0;
        }
        
        #faqs li {
            margin-bottom: 15px;
            padding: 20px;
            background-color: #f9fafb;
            border-radius: 10px;
            border-left: 3px solid #3183FF;
        }
        
        #faqs strong {
            color: #3183FF;
            display: block;
            margin-bottom: 5px;
            font-size: 1.1rem;
        }
        
        /* Thank you modal */
        #thankYouModal {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.8);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 9999;
        }
        
        .modal-content {
            background: white;
            padding: 40px;
            border-radius: 15px;
            text-align: center;
            max-width: 450px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.25);
            transform: scale(0.9);
            animation: pop 0.3s forwards;
            border-top: 4px solid #3183FF;
        }
        
        @keyframes pop {
            to { transform: scale(1); }
        }
        
        .modal-content h2 {
            color: #3183FF;
            margin-top: 0;
            font-size: 1.8rem;
        }
        
        .modal-content p {
            margin: 15px 0;
            color: #4d5b75;
        }
        
        .modal-content .appointment-id {
            background-color: #f0f7ff;
            padding: 15px;
            border-radius: 8px;
            margin: 20px 0;
            font-weight: 600;
            color: #3183FF;
            font-size: 1.1rem;
            border-left: 3px solid #3183FF;
        }
        
        .modal-button {
            margin-top: 20px;
            padding: 12px 25px;
            background: #3183FF;
            color: white;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .modal-button:hover {
            background: #2468d9;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(49, 131, 255, 0.3);
        }
        
        /* Additional responsive adjustments */
        @media (max-width: 767px) {
            #appointment-section {
                padding: 20px 15px;
                margin-bottom: 40px;
            }
            
            .appointment-header h1 {
                font-size: 1.6rem;
            }
            
            .tab {
                padding: 20px 15px;
            }
            
            #appointment-section button[type="submit"] {
                padding: 12px 20px;
            }
        }

        /* Add these styles to the existing CSS */
        .time-slot {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .availability-indicator {
            display: inline-block;
            margin-left: 10px;
            font-size: 0.8rem;
            padding: 2px 6px;
            border-radius: 10px;
            font-weight: bold;
        }

        .available-3 {
            background-color: #d4edda;
            color: #155724;
        }

        .available-2 {
            background-color: #fff3cd;
            color: #856404;
        }

        .available-1 {
            background-color: #f8d7da;
            color: #721c24;
        }

        .available-0 {
            background-color: #6c757d;
            color: white;
        }

        /* Make the select box wider to accommodate the indicators */
        #preferred_time, #new_pet_preferred_time {
            min-width: 200px;
        }

        /* Loading spinner */
        .loading-spinner {
            display: inline-block;
            width: 1rem;
            height: 1rem;
            border: 2px solid rgba(0, 0, 0, 0.1);
            border-left-color: #09f;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin-left: 10px;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }
    </style>
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
            <?php if ($user): ?>
            <span class="d-block text-primary fs-3 fw-semibold mb-2 fade-in-up" style="animation-delay: 0.2s;">
                Welcome, <?php echo htmlspecialchars($user['name']); ?>!
            </span>
            <?php endif; ?>
            <span class="fade-in-up" style="animation-delay: 0.4s;">
                Book Your Pet's Appointment
            </span>
            </h1>

            <p class="lead fade-in-up" style="animation-delay: 0.6s;">
            Quick, Easy, Hassle-Free. Secure your slot and get the care your pet deserves — all in just a few clicks.
            </p>

            <div class="fade-in-up" style="animation-delay: 0.8s;">
                <a href="#appointment-section" class="btn btn-primary mt-3">
                    Book Now!
                </a>
                <?php if ($user): ?>
                <a href="/my-appointments" class="btn btn-outline-primary mt-3 ms-2">
                    <i class="fas fa-calendar-check me-1"></i> My Appointments
                </a>
                <?php endif; ?>
            </div>
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

        <?php if (!isset($user)): ?>
            <div class="alert alert-info">
                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="alert-icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                <p>Please <a href="/login">log in</a> to book an appointment. If you don't have an account, you can <a href="/register">register here</a>.</p>
            </div>
        <?php else: ?>
            <?php if (isset($confirmation_message)): ?>
                <div id="confirmation-message">
                    <p><?php echo $confirmation_message; ?></p>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($errors)): ?>
                <div class="error-message">
                    <strong>Please correct the following errors:</strong>
                    <ul>
                        <?php foreach ($errors as $error): ?>
                            <li><?php echo htmlspecialchars($error); ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
            <?php endif; ?>
            
            <!-- Tab Buttons -->
            <div class="tab-buttons">
                <div id="existing-pet-button" class="tab-button <?php echo $activeTab === 'existing-pet' ? 'active' : ''; ?>">Book with Existing Pet</div>
                <div id="new-pet-button" class="tab-button <?php echo $activeTab === 'new-pet' ? 'active' : ''; ?>">Book with New Pet</div>
            </div>
            
            <!-- Existing Pet Tab -->
            <div id="existing-pet-tab" class="tab <?php echo $activeTab === 'existing-pet' ? 'active' : ''; ?>">
                <?php if (empty($pets)): ?>
                    <div class="alert alert-info">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="alert-icon"><circle cx="12" cy="12" r="10"></circle><line x1="12" y1="16" x2="12" y2="12"></line><line x1="12" y1="8" x2="12.01" y2="8"></line></svg>
                        <p>You don't have any pets registered yet. Please use the "Book with New Pet" option.</p>
                    </div>
                <?php else: ?>
                    <form id="appointment-form" method="POST" action="/appointment">
                        <input type="hidden" name="form_type" value="existing_pet">
                        
                        <!-- Select Pet -->
                        <div class="form-group">
                            <label for="pet_code">Select Your Pet:</label>
                            <select id="pet_code" name="pet_code" required>
                                <option value="">-- Select a Pet --</option>
                                <?php foreach ($pets as $pet): ?>
                                <option value="<?php echo htmlspecialchars($pet['pet_code']); ?>" <?php echo (isset($form_data['pet_code']) && $form_data['pet_code'] == $pet['pet_code']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($pet['pet_name']); ?> (<?php echo htmlspecialchars($pet['pet_type']); ?>)
                                </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        
                        <!-- Service -->
                        <div class="form-group">
                            <label for="service">Select Service:</label>
                            <select id="service" name="service" required>
                                <option value="">-- Select a Service --</option>
                                <?php if (!empty($services)): ?>
                                    <?php foreach($services as $service): ?>
                                    <option value="<?php echo htmlspecialchars($service['service_name']); ?>" 
                                            data-code="<?php echo htmlspecialchars($service['service_code']); ?>"
                                            <?php echo (isset($form_data['service']) && $form_data['service'] === $service['service_name']) ? 'selected' : ''; ?>>
                                        <?php echo htmlspecialchars($service['service_name']); ?> (₱<?php echo number_format($service['service_fee'], 2); ?>)
                                    </option>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </select>
                        </div>

                        <!-- Appointment Type -->
                        <div class="form-group">
                            <label for="appointment_type">Type of Appointment:</label>
                            <select id="appointment_type" name="appointment_type" required>
                                <option value="">-- Select Type --</option>
                                <option value="walk-in" <?php echo (isset($form_data['appointment_type']) && $form_data['appointment_type'] === 'walk-in') ? 'selected' : ''; ?>>Walk-in</option>
                                <option value="service-on-call" <?php echo (isset($form_data['appointment_type']) && $form_data['appointment_type'] === 'service-on-call') ? 'selected' : ''; ?>>Service-on-call</option>
                            </select>
                        </div>

                        <!-- Preferred Date -->
                        <div class="form-group">
                            <label for="preferred_date">Preferred Date:</label>
                            <input type="date" id="preferred_date" name="preferred_date" min="<?php echo $today; ?>" value="<?php echo isset($form_data['preferred_date']) ? htmlspecialchars($form_data['preferred_date']) : ''; ?>" required>
                        </div>

                        <!-- Preferred Time -->
                        <div class="form-group">
                            <label for="preferred_time">Preferred Time:</label>
                            <div class="time-select-container">
                                <select id="preferred_time" name="preferred_time" required>
                                    <option value="">-- Select Time --</option>
                                    <option value="09:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '09:00:00') ? 'selected' : ''; ?>>9:00 AM</option>
                                    <option value="10:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '10:00:00') ? 'selected' : ''; ?>>10:00 AM</option>
                                    <option value="11:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '11:00:00') ? 'selected' : ''; ?>>11:00 AM</option>
                                    <option value="13:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '13:00:00') ? 'selected' : ''; ?>>1:00 PM</option>
                                    <option value="14:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '14:00:00') ? 'selected' : ''; ?>>2:00 PM</option>
                                    <option value="15:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '15:00:00') ? 'selected' : ''; ?>>3:00 PM</option>
                                    <option value="16:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '16:00:00') ? 'selected' : ''; ?>>4:00 PM</option>
                                </select>
                                <span id="availability-indicator" class="availability-indicator"></span>
                            </div>
                            <small class="form-text text-muted">Maximum 3 appointments per time slot</small>
                        </div>

                        <!-- Additional Notes -->
                        <div class="form-group full-width">
                            <label for="additional_notes">Additional Notes:</label>
                            <textarea id="additional_notes" name="additional_notes" placeholder="Please share any specific concerns or information about your pet's condition"><?php echo isset($form_data['additional_notes']) ? htmlspecialchars($form_data['additional_notes']) : ''; ?></textarea>
                        </div>

                        <!-- Reminder (optional) -->
                        <div class="form-group full-width checkbox-container">
                            <input type="checkbox" id="reminder" name="reminder" <?php echo (isset($form_data['reminder'])) ? 'checked' : ''; ?>>
                            <label for="reminder">Receive appointment reminder via SMS/email</label>
                        </div>
                        
                        <!-- Submit Button -->
                        <div class="form-group full-width">
                            <button type="submit">Book Appointment</button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
            
            <!-- New Pet Tab -->
            <div id="new-pet-tab" class="tab <?php echo $activeTab === 'new-pet' ? 'active' : ''; ?>">
                <form id="new-pet-appointment-form" method="POST" action="/appointment">
                    <input type="hidden" name="form_type" value="new_pet">
                    
                    <!-- Pet Name -->
                    <div class="form-group">
                        <label for="pet_name">Pet Name:</label>
                        <input type="text" id="pet_name" name="pet_name" value="<?php echo isset($form_data['pet_name']) ? htmlspecialchars($form_data['pet_name']) : ''; ?>" required>
                    </div>
                    
                    <!-- Pet Type -->
                    <div class="form-group">
                        <label for="pet_type">Pet Type:</label>
                        <select id="pet_type" name="pet_type" required>
                            <option value="">-- Select Pet Type --</option>
                            <option value="Dog" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Dog') ? 'selected' : ''; ?>>Dog</option>
                            <option value="Cat" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Cat') ? 'selected' : ''; ?>>Cat</option>
                            <option value="Bird" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Bird') ? 'selected' : ''; ?>>Bird</option>
                            <option value="Rabbit" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Rabbit') ? 'selected' : ''; ?>>Rabbit</option>
                            <option value="Hamster/Guinea Pig" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Hamster/Guinea Pig') ? 'selected' : ''; ?>>Hamster/Guinea Pig</option>
                            <option value="Reptile" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Reptile') ? 'selected' : ''; ?>>Reptile</option>
                            <option value="Fish" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Fish') ? 'selected' : ''; ?>>Fish</option>
                            <option value="Other" <?php echo (isset($form_data['pet_type']) && $form_data['pet_type'] === 'Other') ? 'selected' : ''; ?>>Other</option>
                        </select>
                    </div>
                    
                    <!-- Pet Breed -->
                    <div class="form-group">
                        <label for="pet_breed">Pet Breed:</label>
                        <input type="text" id="pet_breed" name="pet_breed" value="<?php echo isset($form_data['pet_breed']) ? htmlspecialchars($form_data['pet_breed']) : ''; ?>">
                    </div>
                    
                    <!-- Pet Age -->
                    <div class="form-group">
                        <label for="pet_age">Pet Age (years):</label>
                        <input type="number" id="pet_age" name="pet_age" min="0" value="<?php echo isset($form_data['pet_age']) ? htmlspecialchars($form_data['pet_age']) : ''; ?>" required>
                    </div>
                    
                    <!-- Medical History -->
                    <div class="form-group full-width">
                        <label for="pet_med_history">Medical History (optional):</label>
                        <textarea id="pet_med_history" name="pet_med_history" placeholder="Please provide any previous medical conditions, allergies, or treatments"><?php echo isset($form_data['pet_med_history']) ? htmlspecialchars($form_data['pet_med_history']) : ''; ?></textarea>
                    </div>
                    
                    <div class="form-group full-width">
                        <hr>
                        <h3>Appointment Details</h3>
                    </div>
                    
                    <!-- Service -->
                    <div class="form-group">
                        <label for="new_pet_service">Select Service:</label>
                        <select id="new_pet_service" name="service" required>
                            <option value="">-- Select a Service --</option>
                            <?php if (!empty($services)): ?>
                                <?php foreach($services as $service): ?>
                                <option value="<?php echo htmlspecialchars($service['service_name']); ?>" 
                                        data-code="<?php echo htmlspecialchars($service['service_code']); ?>"
                                        <?php echo (isset($form_data['service']) && $form_data['service'] === $service['service_name']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($service['service_name']); ?> (₱<?php echo number_format($service['service_fee'], 2); ?>)
                                </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>

                    <!-- Appointment Type -->
                    <div class="form-group">
                        <label for="new_pet_appointment_type">Type of Appointment:</label>
                        <select id="new_pet_appointment_type" name="appointment_type" required>
                            <option value="">-- Select Type --</option>
                            <option value="walk-in" <?php echo (isset($form_data['appointment_type']) && $form_data['appointment_type'] === 'walk-in') ? 'selected' : ''; ?>>Walk-in</option>
                            <option value="service-on-call" <?php echo (isset($form_data['appointment_type']) && $form_data['appointment_type'] === 'service-on-call') ? 'selected' : ''; ?>>Service-on-call</option>
                        </select>
                    </div>

                    <!-- Preferred Date -->
                    <div class="form-group">
                        <label for="new_pet_preferred_date">Preferred Date:</label>
                        <input type="date" id="new_pet_preferred_date" name="preferred_date" min="<?php echo $today; ?>" value="<?php echo isset($form_data['preferred_date']) ? htmlspecialchars($form_data['preferred_date']) : ''; ?>" required>
                    </div>

                    <!-- Preferred Time -->
                    <div class="form-group">
                        <label for="new_pet_preferred_time">Preferred Time:</label>
                        <div class="time-select-container">
                            <select id="new_pet_preferred_time" name="preferred_time" required>
                                <option value="">-- Select Time --</option>
                                <option value="09:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '09:00:00') ? 'selected' : ''; ?>>9:00 AM</option>
                                <option value="10:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '10:00:00') ? 'selected' : ''; ?>>10:00 AM</option>
                                <option value="11:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '11:00:00') ? 'selected' : ''; ?>>11:00 AM</option>
                                <option value="13:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '13:00:00') ? 'selected' : ''; ?>>1:00 PM</option>
                                <option value="14:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '14:00:00') ? 'selected' : ''; ?>>2:00 PM</option>
                                <option value="15:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '15:00:00') ? 'selected' : ''; ?>>3:00 PM</option>
                                <option value="16:00:00" <?php echo (isset($form_data['preferred_time']) && $form_data['preferred_time'] === '16:00:00') ? 'selected' : ''; ?>>4:00 PM</option>
                            </select>
                            <span id="new-pet-availability-indicator" class="availability-indicator"></span>
                        </div>
                        <small class="form-text text-muted">Maximum 3 appointments per time slot</small>
                    </div>

                    <!-- Additional Notes -->
                    <div class="form-group full-width">
                        <label for="new_pet_additional_notes">Additional Notes:</label>
                        <textarea id="new_pet_additional_notes" name="additional_notes" placeholder="Please share any specific concerns or requests for your appointment"><?php echo isset($form_data['additional_notes']) ? htmlspecialchars($form_data['additional_notes']) : ''; ?></textarea>
                    </div>

                    <!-- Reminder (optional) -->
                    <div class="form-group full-width checkbox-container">
                        <input type="checkbox" id="new_pet_reminder" name="reminder" <?php echo (isset($form_data['reminder'])) ? 'checked' : ''; ?>>
                        <label for="new_pet_reminder">Receive appointment reminder via SMS/email</label>
                    </div>
                    
                    <!-- Submit Button -->
                    <div class="form-group full-width">
                        <button type="submit">Add Pet & Book Appointment</button>
                    </div>
                </form>
            </div>
        </div>
    <?php endif; ?>
</section>

<!-- FAQs Section -->
<section id="faqs">
    <h2>Frequently Asked Questions</h2>
    <ul class="faq-list">
        <li>
            <strong>How do I reschedule my appointment?</strong>
            <p>You can reschedule by contacting our clinic at least 24 hours before your scheduled time. Please call us at (123) 456-7890 or email us at contact@mavetcare.com with your appointment ID.</p>
        </li>
        <li>
            <strong>What are your clinic hours?</strong>
            <p>Our clinic is open Monday to Saturday, from 9 AM to 5 PM. We're closed on Sundays and public holidays.</p>
        </li>
        <li>
            <strong>Do I need to bring anything to my appointment?</strong>
            <p>Please bring any previous medical records for your pet, especially for first-time visits. For returning patients, your appointment ID will help us retrieve your records quickly.</p>
        </li>
        <li>
            <strong>How long will my appointment take?</strong>
            <p>Standard check-ups typically take 30 minutes. Specialized services like grooming or procedures may take longer. We'll provide time estimates when you book.</p>
        </li>
        <li>
            <strong>What payment methods do you accept?</strong>
            <p>We accept cash, all major credit/debit cards, and mobile payment options. Payment is typically collected after the service is provided.</p>
        </li>
    </ul>
</section>

<!-- Thank You Modal -->
<?php if ($showThankYou): ?>
<div id="thankYouModal">
    <div class="modal-content">
        <h2>🎉 Appointment Booked!</h2>
        <p>Thank you for booking with MaVetCare. We've received your appointment request and our team will review it shortly.</p>
        <div class="appointment-id">#<?php echo htmlspecialchars($appointment_id); ?></div>
        <p>Please save your appointment ID for reference. A confirmation email has been sent to your registered email address.</p>
        <button class="modal-button" onclick="closeThankYou()">Continue</button>
    </div>
</div>
<?php endif; ?>

<script>
<?php if ($showThankYou): ?>
function closeThankYou() {
    document.getElementById('thankYouModal').style.display = 'none';
    window.location.href = '/appointment';
}
<?php endif; ?>

// Global variable to store availability data
let availabilityData = null;

// Function to fetch availability data
function fetchAvailability(date) {
    // Show loading indicator
    document.getElementById('availability-indicator').innerHTML = '<div class="loading-spinner"></div>';
    document.getElementById('new-pet-availability-indicator').innerHTML = '<div class="loading-spinner"></div>';
    
    // Disable time selects while loading
    document.getElementById('preferred_time').disabled = true;
    document.getElementById('new_pet_preferred_time').disabled = true;
    
    fetch(`/api/appointment/availability?date=${date}`, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        availabilityData = data;
        updateTimeSlots('preferred_time', 'availability-indicator');
        updateTimeSlots('new_pet_preferred_time', 'new-pet-availability-indicator');
        
        // Re-enable time selects
        document.getElementById('preferred_time').disabled = false;
        document.getElementById('new_pet_preferred_time').disabled = false;
    })
    .catch(error => {
        console.error('Error fetching availability:', error);
        document.getElementById('availability-indicator').textContent = '';
        document.getElementById('new-pet-availability-indicator').textContent = '';
        
        // Re-enable time selects
        document.getElementById('preferred_time').disabled = false;
        document.getElementById('new_pet_preferred_time').disabled = false;
    });
}

// Function to update time slot options with availability info
function updateTimeSlots(selectId, indicatorId) {
    const select = document.getElementById(selectId);
    const indicator = document.getElementById(indicatorId);
    
    if (!select || !availabilityData) return;
    
    // Update all options to show availability
    Array.from(select.options).forEach(option => {
        if (option.value && availabilityData.slots[option.value]) {
            const slot = availabilityData.slots[option.value];
            const available = slot.available;
            
            // If this option is selected, update the indicator
            if (option.selected && option.value) {
                updateAvailabilityIndicator(indicator, available);
            }
            
            // Update the option text to include availability
            if (available <= 0) {
                option.text = `${slot.label} (Full)`;
                option.disabled = true;
            } else {
                option.text = `${slot.label} (${available} left)`;
                option.disabled = false;
            }
        }
    });
}

// Function to update the availability indicator
function updateAvailabilityIndicator(indicator, available) {
    indicator.className = 'availability-indicator';
    
    if (available <= 0) {
        indicator.textContent = 'Full';
        indicator.classList.add('available-0');
    } else {
        indicator.textContent = `${available} slot${available !== 1 ? 's' : ''} left`;
        indicator.classList.add(`available-${available}`);
    }
}

// Handle date change
function handleDateChange(dateInput, timeSelect, indicatorId) {
    const date = dateInput.value;
    if (date) {
        fetchAvailability(date);
    } else {
        // Clear indicators if no date selected
        document.getElementById(indicatorId).textContent = '';
    }
}

// Handle time selection change
function handleTimeChange(timeSelect, indicatorId) {
    const selectedTime = timeSelect.value;
    const indicator = document.getElementById(indicatorId);
    
    if (selectedTime && availabilityData && availabilityData.slots[selectedTime]) {
        const available = availabilityData.slots[selectedTime].available;
        updateAvailabilityIndicator(indicator, available);
    } else {
        indicator.textContent = '';
    }
}

function showTab(tabName) {
    console.log("Switching to tab:", tabName); // Debug
    
    // Hide all tabs
    document.querySelectorAll('.tab').forEach(tab => {
        tab.classList.remove('active');
    });
    
    // Deactivate all buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('active');
    });
    
    // Show selected tab
    if (tabName === 'existing-pet') {
        const tab = document.getElementById('existing-pet-tab');
        const button = document.getElementById('existing-pet-button');
        
        if (tab) tab.classList.add('active');
        if (button) button.classList.add('active');
    } else if (tabName === 'new-pet') {
        const tab = document.getElementById('new-pet-tab');
        const button = document.getElementById('new-pet-button');
        
        if (tab) tab.classList.add('active');
        if (button) button.classList.add('active');
    }
}

// Ensure tabs work on page load
document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM loaded, setting up tabs"); // Debug
    
    // Check that all elements exist
    const existingPetTab = document.getElementById('existing-pet-tab');
    const newPetTab = document.getElementById('new-pet-tab');
    const existingPetButton = document.getElementById('existing-pet-button');
    const newPetButton = document.getElementById('new-pet-button');
    
    if (!existingPetTab) console.error("Missing element: existing-pet-tab");
    if (!newPetTab) console.error("Missing element: new-pet-tab");
    if (!existingPetButton) console.error("Missing element: existing-pet-button");
    if (!newPetButton) console.error("Missing element: new-pet-button");
    
    // Direct click handlers for buttons
    if (existingPetButton) {
        existingPetButton.onclick = function(e) {
            e.preventDefault();
            showTab('existing-pet');
        };
    }
    
    if (newPetButton) {
        newPetButton.onclick = function(e) {
            e.preventDefault();
            showTab('new-pet');
        };
    }

    // Set active tab based on PHP variable
    <?php if ($activeTab === 'new-pet'): ?>
    showTab('new-pet');
    <?php else: ?>
    showTab('existing-pet');
    <?php endif; ?>
    
    // Set up event listeners for date and time inputs
    const preferredDate = document.getElementById('preferred_date');
    const preferredTime = document.getElementById('preferred_time');
    const newPetPreferredDate = document.getElementById('new_pet_preferred_date');
    const newPetPreferredTime = document.getElementById('new_pet_preferred_time');
    
    if (preferredDate) {
        preferredDate.addEventListener('change', function() {
            handleDateChange(this, preferredTime, 'availability-indicator');
        });
        
        // Load initial availability if date is already set
        if (preferredDate.value) {
            fetchAvailability(preferredDate.value);
        }
    }
    
    if (preferredTime) {
        preferredTime.addEventListener('change', function() {
            handleTimeChange(this, 'availability-indicator');
        });
    }
    
    if (newPetPreferredDate) {
        newPetPreferredDate.addEventListener('change', function() {
            handleDateChange(this, newPetPreferredTime, 'new-pet-availability-indicator');
        });
    }
    
    if (newPetPreferredTime) {
        newPetPreferredTime.addEventListener('change', function() {
            handleTimeChange(this, 'new-pet-availability-indicator');
        });
    }
});
</script>

<!-- Footer Section (Same as Home Page) -->
<?php include_once '../app/views/includes/footer.php'; ?>
</body>
</html>
