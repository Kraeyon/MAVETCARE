<?php

namespace App\Controllers;

use App\Models\AppointmentModel;
use Config\Database;

class AppointmentController extends BaseController {
    private $appointmentModel;
    private $db;
    
    public function __construct() {
        $this->appointmentModel = new AppointmentModel();
        $this->db = \Config\Database::getInstance()->getConnection();
    }
    
    /**
     * Show the appointment booking page
     */
    public function index() {
        // Check if user is logged in
        $user = $this->getUser();
        if (!$user) {
            // Redirect to login page if not logged in
            $_SESSION['redirect_after_login'] = '/appointment';
            header('Location: /login');
            exit;
        }
        
        // User is logged in, get their pets
        $userData = [
            'user' => $user,
            'pets' => []
        ];
        
        // If it's a regular client (not admin), get their pets
        if ($user['role'] !== 'admin' && !empty($user['client_code'])) {
            // Get all pets for this client
            $pdo = $this->getPDO();
            $sql = "SELECT * FROM pet WHERE client_code = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user['client_code']]);
            $userData['pets'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        // Render the page with user data
        $this->render('home/appointmentpage', $userData);
    }
    
    /**
     * Process appointment form submission
     */
    public function submitAppointment() {
        // Add debug message to confirm this method is called
        error_log("***********************");
        error_log("SUBMIT APPOINTMENT METHOD CALLED IN APPOINTMENTCONTROLLER");
        error_log("***********************");
        
        // Debug database connection
        try {
            $testDb = $this->getPDO();
            error_log("Database connection test: " . ($testDb ? "Success" : "Failed"));
            
            // Test simple query
            $testQuery = $testDb->query("SELECT 1");
            error_log("Test query result: " . ($testQuery ? "Success" : "Failed"));
        } catch (\Exception $e) {
            error_log("Database connection exception: " . $e->getMessage());
        }
        
        // Check if user is logged in
        $user = $this->getUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }
        
        // Process form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // For debugging
            error_log("Form submission received: " . print_r($_POST, true));
            
            // Initialize
            $errors = [];
            $client_code = $user['client_code'];
            
            // First check if the appointment table structure is valid
            if (!$this->appointmentModel->checkAppointmentTableStructure()) {
                error_log("Appointment table structure is invalid!");
                $errors[] = "Database error: Appointment table structure is invalid. Please contact support.";
                // Render the form with errors
                $this->render('home/appointmentpage', [
                    'user' => $user,
                    'pets' => [],
                    'errors' => $errors,
                    'form_data' => $_POST
                ]);
                return;
            }
            
            // Get client information
            $clientData = $this->appointmentModel->getClientInfo($client_code);
            $contact_number = $clientData['clt_contact'] ?? '';
            $email = $clientData['clt_email_address'] ?? '';
            
            // Helper function for sanitizing input
            function sanitize($field) {
                return isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field]), ENT_QUOTES, 'UTF-8') : '';
            }
            
            // Get form type
            $form_type = sanitize('form_type');
            error_log("Form type: " . $form_type);
            
            // Initialize pet_code variable
            $pet_code = null;
            
            // Process existing pet form
            if ($form_type === 'existing_pet') {
                error_log("Processing existing pet form");
                
                // Get form data for existing pet
                $pet_code = sanitize('pet_code');
                $service = sanitize('service');
                $preferred_date = sanitize('preferred_date');
                $preferred_time = sanitize('preferred_time');
                $appointment_type = sanitize('appointment_type');
                $additional_notes = sanitize('additional_notes');
                
                // Get pet details
                $petData = $this->appointmentModel->getPetById($pet_code);
                $pet_name = $petData['pet_name'] ?? '';
                $pet_type = $petData['pet_type'] ?? '';
                
                // Validation
                if (!$pet_code) $errors[] = "Please select a pet.";
                
            } 
            // Process new pet form
            elseif ($form_type === 'new_pet') {
                error_log("Processing new pet form");
                
                // Get new pet form data
                $pet_name = sanitize('pet_name');
                $pet_type = sanitize('pet_type');
                $pet_breed = sanitize('pet_breed');
                $pet_age = (int)sanitize('pet_age');
                $pet_med_history = sanitize('pet_med_history');
                $service = sanitize('service');
                $preferred_date = sanitize('preferred_date');
                $preferred_time = sanitize('preferred_time');
                $appointment_type = sanitize('appointment_type');
                $additional_notes = sanitize('additional_notes');
                
                // Validate pet details
                if (empty($pet_name)) $errors[] = "Pet name is required";
                if (empty($pet_type)) $errors[] = "Pet type is required";
                if ($pet_age <= 0) $errors[] = "Please enter a valid age";
                
                // If pet details valid, add the pet first
                if (empty($errors)) {
                    try {
                        // Check for duplicate pet
                        if ($this->appointmentModel->isDuplicatePet($client_code, $pet_name, $pet_type)) {
                            $errors[] = "You already have a pet with this name and type";
                        } else {
                            // Add the pet
                            error_log("Inserting new pet: $pet_name, $pet_type, $pet_breed, $pet_age");
                            
                            $petData = [
                                'client_code' => $client_code,
                                'pet_name' => $pet_name,
                                'pet_type' => $pet_type,
                                'pet_breed' => $pet_breed,
                                'pet_age' => $pet_age,
                                'pet_med_history' => $pet_med_history
                            ];
                            
                            $pet_code = $this->appointmentModel->addPet($petData);
                            
                            if (!$pet_code) {
                                error_log("Pet was added but no pet_code was returned");
                                $errors[] = "Failed to retrieve pet ID. Please try again.";
                            }
                        }
                    } catch (\Exception $e) {
                        error_log("Exception adding pet: " . $e->getMessage());
                        $errors[] = "Error adding pet: " . $e->getMessage();
                    }
                }
            } else {
                error_log("Invalid form type: $form_type");
                $errors[] = "Invalid form submission.";
            }
            
            // Common validation for both forms
            if (!$service) $errors[] = "Service is required.";
            if (!$preferred_date) $errors[] = "Preferred date is required.";
            if (!$preferred_time) $errors[] = "Preferred time is required.";
            if (!$appointment_type) $errors[] = "Appointment type is required.";
            
            // Validate date is not in the past
            $today = date('Y-m-d');
            if ($preferred_date < $today) {
                $errors[] = "Appointment date cannot be in the past.";
            }
            
            // Validate time is within business hours (9AM-5PM)
            $hour = (int)date('G', strtotime($preferred_time));
            if ($hour < 9 || $hour > 16) {
                $errors[] = "Appointment time must be between 9:00 AM and 5:00 PM.";
            }
            
            error_log("Validation errors: " . print_r($errors, true));
            
            // Proceed if valid
            if (empty($errors)) {
                try {
                    // Check availability of the time slot
                    if (!$this->appointmentModel->isTimeSlotAvailable($preferred_date, $preferred_time)) {
                        $errors[] = "Sorry, the selected time slot is already full. Please choose another.";
                    } else {
                        // Save appointment
                        error_log("Saving appointment for pet_code: $pet_code");
                        
                        // Look up service code based on service name
                        $service_code = $this->getServiceCode($service);
                        
                        // Get pet details to include pet name and type
                        $petData = $this->appointmentModel->getPetById($pet_code);
                        
                        try {
                            // Get client information for contact details
                            $clientData = $this->appointmentModel->getClientInfo($client_code);
                            $contact_number = $clientData['clt_contact'] ?? '';
                            $email = $clientData['clt_email_address'] ?? '';
                            
                            // Prepare owner name from client data
                            $owner_name = '';
                            if (!empty($clientData['clt_fname'])) {
                                $owner_name .= trim($clientData['clt_fname']) . ' ';
                            }
                            if (!empty($clientData['clt_initial'])) {
                                $owner_name .= trim($clientData['clt_initial']) . ' ';
                            }
                            if (!empty($clientData['clt_lname'])) {
                                $owner_name .= trim($clientData['clt_lname']);
                            }
                            $owner_name = trim($owner_name);
                            
                            // Log the values being sent
                            error_log("APPOINTMENT DATA - owner_name: " . $owner_name);
                            error_log("APPOINTMENT DATA - contact_number: " . $contact_number);
                            error_log("APPOINTMENT DATA - email: " . $email);
                            error_log("APPOINTMENT DATA - additional_notes: " . $additional_notes);
                        
                            // More focused approach - create a simple appointment first
                            $minimalData = [
                                ':client_code' => $client_code,
                                ':pet_code' => $pet_code,
                                ':service_code' => $service_code,
                                ':preferred_date' => $preferred_date,
                                ':preferred_time' => $preferred_time,
                                ':appointment_type' => $appointment_type,
                                ':additional_notes' => $additional_notes,
                                ':contact_number' => $contact_number,
                                ':owner_name' => $owner_name,
                                ':email' => $email
                            ];
                            
                            error_log("Submitting appointment with minimal data: " . print_r($minimalData, true));
                            
                            $appointment_id = $this->appointmentModel->createAppointment($minimalData);
                            
                            error_log("APPOINTMENT CREATION RESULT: " . ($appointment_id ? "SUCCESS with ID: $appointment_id" : "FAILED - no ID returned"));
                            
                            if ($appointment_id) {
                                error_log("Appointment created with ID: $appointment_id");
                                
                                // Send confirmation email (client + admin)
                                $this->sendConfirmationEmails($user['name'], $pet_name, $service, $preferred_date, $preferred_time, $appointment_id, $email);
                                
                                // Set success session - with extra debug info
                                error_log("Setting session variables for success message");
                                $_SESSION['appointment_success'] = true;
                                $_SESSION['appointment_id'] = $appointment_id;
                                
                                error_log("SESSION VALUES SET: appointment_success=" . 
                                    (isset($_SESSION['appointment_success']) ? $_SESSION['appointment_success'] : 'not set') . 
                                    ", appointment_id=" . (isset($_SESSION['appointment_id']) ? $_SESSION['appointment_id'] : 'not set'));
                                
                                // Debug the redirect - Are we actually hitting this code?
                                error_log("About to redirect to /appointment with success");
                                
                                // Add a flush to make sure logs are written
                                flush();
                            } else {
                                error_log("Failed to create appointment: No ID returned");
                                $errors[] = "Failed to create appointment. Please try again.";
                            }
                        } catch (\Exception $e) {
                            error_log("Exception while creating appointment: " . $e->getMessage());
                            $errors[] = "Error creating appointment: " . $e->getMessage();
                        }
                    }
                } catch (\Exception $e) {
                    error_log("Exception saving appointment: " . $e->getMessage());
                    $errors[] = "Error saving appointment: " . $e->getMessage();
                }
            }
            
            // If there were errors, go back to the form with error messages
            if (!empty($errors)) {
                error_log("Returning to form with errors: " . print_r($errors, true));
                
                // Get all pets for this client
                $pdo = $this->getPDO();
                $sql = "SELECT * FROM pet WHERE client_code = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$client_code]);
                $pets = $stmt->fetchAll(\PDO::FETCH_ASSOC);
                
                // Render the form with errors
                $this->render('home/appointmentpage', [
                    'user' => $user,
                    'pets' => $pets,
                    'errors' => $errors,
                    'form_data' => $_POST
                ]);
                return;
            }
            
            // Success! Redirect back to appointment page
            error_log("SUCCESS! No errors, redirecting to /appointment with session vars set");
            
            // Make sure headers haven't been sent yet
            if (!headers_sent()) {
                header('Location: /appointment');
                exit;
            } else {
                error_log("WARNING: Headers already sent, cannot redirect. Using JavaScript instead.");
                echo '<script>window.location.href = "/appointment";</script>';
                exit;
            }
        } else {
            // Not a POST request
            error_log("Not a POST request, redirecting to appointment page");
            header('Location: /appointment');
            exit;
        }
    }
    
    /**
     * Send confirmation emails to client and admin
     */
    private function sendConfirmationEmails($owner_name, $pet_name, $service, $preferred_date, $preferred_time, $appointment_id, $email) {
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
                <li><strong>Pet:</strong> $pet_name</li>
                <li><strong>Service:</strong> $service</li>
                <li><strong>Date & Time:</strong> $preferred_date at $preferred_time</li>
            </ul>
        ";
        
        // Get admin email from environment variable or use default
        $admin_email = $_ENV['ADMIN_EMAIL'] ?? 'admin@mavetcare.com';
        
        // Attempt to send emails, but continue if they fail
        try {
            $this->sendEmail($email, $user_subject, $user_message);
            $this->sendEmail($admin_email, $admin_subject, $admin_message);
        } catch (\Exception $e) {
            error_log("Error sending email: " . $e->getMessage());
        }
    }
    
    /**
     * Send an email
     */
    private function sendEmail($to, $subject, $message) {
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/html; charset=UTF-8\r\n";
        $sender_email = $_ENV['SENDER_EMAIL'] ?? 'noreply@mavetcare.com';
        $headers .= "From: $sender_email\r\n";
        mail($to, $subject, $message, $headers);
    }
    
    /**
     * View all upcoming appointments (admin only)
     */
    public function viewAppointments() {
        // Check if user is admin
        $user = $this->getUser();
        if (!$user || $user['role'] !== 'admin') {
            header('Location: /login');
            exit;
        }
        
        // Get requested date or use today
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        
        // Get appointments for the date
        $appointments = $this->appointmentModel->getAppointmentsByDate($date);
        
        // Render admin appointments view
        $this->render('admin/appointments', [
            'appointments' => $appointments,
            'date' => $date
        ]);
    }
    
    /**
     * Show add pet form
     */
    public function showAddPetForm() {
        // Check if user is logged in
        $user = $this->getUser();
        if (!$user) {
            // Redirect to login page if not logged in
            $_SESSION['redirect_after_login'] = '/add-pet';
            header('Location: /login');
            exit;
        }
        
        // Only clients can add pets
        if ($user['role'] === 'admin') {
            header('Location: /admin/patients');
            exit;
        }
        
        $this->render('home/add_pet', ['user' => $user]);
    }
    
    /**
     * Process add pet form
     */
    public function addPet() {
        // Check if user is logged in
        $user = $this->getUser();
        if (!$user) {
            header('Location: /login');
            exit;
        }
        
        // Only clients can add pets
        if ($user['role'] === 'admin') {
            header('Location: /admin/patients');
            exit;
        }
        
        // Process form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Sanitize inputs
            $pet_name = filter_var(trim($_POST['pet_name']), FILTER_SANITIZE_STRING);
            $pet_type = filter_var(trim($_POST['pet_type']), FILTER_SANITIZE_STRING);
            $pet_breed = filter_var(trim($_POST['pet_breed']), FILTER_SANITIZE_STRING);
            $pet_age = (int)filter_var(trim($_POST['pet_age']), FILTER_SANITIZE_NUMBER_INT);
            $pet_med_history = filter_var(trim($_POST['pet_med_history']), FILTER_SANITIZE_STRING);
            
            // Validate inputs
            $errors = [];
            
            if (empty($pet_name)) {
                $errors[] = "Pet name is required";
            }
            
            if (empty($pet_type)) {
                $errors[] = "Pet type is required";
            }
            
            if ($pet_age <= 0) {
                $errors[] = "Please enter a valid age";
            }
            
            // If no errors, add the pet
            if (empty($errors)) {
                // Check for duplicate pet
                if ($this->appointmentModel->isDuplicatePet($user['client_code'], $pet_name, $pet_type)) {
                    $errors[] = "You already have a pet with this name and type";
                } else {
                    // Add the pet
                    $petData = [
                        'client_code' => $user['client_code'],
                        'pet_name' => $pet_name,
                        'pet_type' => $pet_type,
                        'pet_breed' => $pet_breed,
                        'pet_age' => $pet_age,
                        'pet_med_history' => $pet_med_history
                    ];
                    
                    $success = $this->appointmentModel->addPet($petData);
                    
                    if ($success) {
                        // Redirect to appointment page
                        header('Location: /appointment');
                        exit;
                    } else {
                        $errors[] = "Failed to add pet. Please try again.";
                    }
                }
            }
            
            // If we get here, there were errors
            $this->render('home/add_pet', [
                'user' => $user,
                'errors' => $errors,
                'pet_name' => $pet_name,
                'pet_type' => $pet_type,
                'pet_breed' => $pet_breed,
                'pet_age' => $pet_age,
                'pet_med_history' => $pet_med_history
            ]);
        }
    }
    
    /**
     * Display service-specific pages
     */
    public function vaccination() {
        $this->render('home/vaccination');
    }
    
    public function deworming() {
        $this->render('home/deworming');
    }
    
    public function antiparasitic() {
        $this->render('home/antiparasitic');
    }
    
    public function surgeries() {
        $this->render('home/surgeries');
    }
    
    public function grooming() {
        $this->render('home/grooming');
    }
    
    public function treatment() {
        $this->render('home/treatment');
    }
    
    public function confinement() {
        $this->render('home/confinement');
    }
    
    /**
     * Get service code based on service name
     * 
     * @param string $serviceName The name of the service
     * @return int The service code, or 1 if not found
     */
    private function getServiceCode($serviceName) {
        try {
            $stmt = $this->db->prepare("
                SELECT service_code FROM service 
                WHERE LOWER(service_name) LIKE :service_name
                LIMIT 1
            ");
            
            $stmt->execute([':service_name' => '%' . strtolower($serviceName) . '%']);
            $result = $stmt->fetchColumn();
            
            return $result ? $result : 1; // Return 1 as default if not found
        } catch (\Exception $e) {
            error_log("Error getting service code: " . $e->getMessage());
            return 1; // Default to service_code 1 if there's an error
        }
    }
} 