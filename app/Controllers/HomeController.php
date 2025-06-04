<?php

namespace App\Controllers;

use Config\Database;
use App\Models\PatientModel;

class HomeController extends BaseController {
    public function homepage() {
        $this->render('home/homepage');
    }
    // about page
    public function aboutpage() {
        try {
            // Get PDO connection using the BaseController method
            $pdo = $this->getPDO();
            
            // Fetch active services from the database (limit to 4 for the about page)
            $stmt = $pdo->query("
                SELECT service_code, service_name, service_desc, service_fee, service_img
                FROM service
                WHERE status = 'ACTIVE' OR status IS NULL
                ORDER BY service_name ASC
                LIMIT 4
            ");
            $services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Fetch featured products (limit to 4)
            $productStmt = $pdo->query("
                SELECT prod_code, prod_name, prod_price, prod_image, prod_category
                FROM product
                WHERE (prod_status = 'ACTIVE' OR prod_status IS NULL)
                AND prod_stock > 0
                ORDER BY prod_name ASC
                LIMIT 4
            ");
            $products = $productStmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Render the about page with the fetched data
            $this->render('home/aboutpage', [
                'services' => $services,
                'products' => $products
            ]);
        } catch (\PDOException $e) {
            error_log("Error fetching data for about page: " . $e->getMessage());
            // If there's an error, render the page with empty arrays
            $this->render('home/aboutpage', [
                'services' => [],
                'products' => []
            ]);
        }
    }
    //reviews page
    public function reviews() {
        $this->render('home/reviews');
    }
    // services page
    public function services() {
        try {
            // Get PDO connection using the BaseController method
            $pdo = $this->getPDO();
            
            // Fetch active services from the database
            $stmt = $pdo->query("
                SELECT service_code, service_name, service_desc, service_fee, service_img
                FROM service
                WHERE status = 'ACTIVE' OR status IS NULL
                ORDER BY service_name ASC
            ");
            $services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Render the services page with the fetched services
            $this->render('home/services', ['services' => $services]);
        } catch (\PDOException $e) {
            error_log("Error fetching services: " . $e->getMessage());
            // If there's an error, render the page with an empty services array
            $this->render('home/services', ['services' => []]);
        }
    }
    // products page
    public function products() {
        $this->render('home/products');
    }
    // appointment page
    public function appointment() {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            // Redirect to login page if not logged in
            $_SESSION['redirect_after_login'] = '/appointment';
            header('Location: /login');
            exit;
        }
        
        // User is logged in, get their pets
        $user = $_SESSION['user'];
        $userData = [
            'user' => $user,
            'pets' => []
        ];
        
        // If it's a regular client (not admin), get their pets
        if ($user['role'] !== 'admin' && !empty($user['client_code'])) {
            // Get PDO connection
            $pdo = Database::getInstance()->getConnection();
            
            // Get patient model
            $patientModel = new PatientModel($pdo);
            
            // Get all pets for this client
            $sql = "SELECT * FROM pet WHERE client_code = ?";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$user['client_code']]);
            $userData['pets'] = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        // Render the page with user data
        $this->render('home/appointmentpage', $userData);
    }
    // handle appointment form submission
    public function submitAppointment() {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        // Process form submission
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // For debugging
            error_log("Form submission received: " . print_r($_POST, true));
            
            // Initialize
            $errors = [];
            $success = false;
            $conn = null;
            
            // Get database connection using the Database class
            function getDBConnection() {
                try {
                    $db = Database::getInstance();
                    return $db->getConnection();
                } catch(\PDOException $e) {
                    error_log("DB Connection Error: " . $e->getMessage());
                    die("Database connection failed: " . $e->getMessage());
                }
            }
            
            // Sanitize helper
            function sanitize($field) {
                return isset($_POST[$field]) ? htmlspecialchars(trim($_POST[$field]), ENT_QUOTES, 'UTF-8') : '';
            }
            
            // Get user data from session
            $user = $_SESSION['user'];
            $owner_name = $user['name'];
            $client_code = $user['client_code'];
            $conn = getDBConnection();
            
            // Get contact and email from the client record
            $stmt = $conn->prepare("SELECT clt_contact, clt_email_address FROM client WHERE clt_code = :client_code");
            $stmt->execute([':client_code' => $client_code]);
            $clientData = $stmt->fetch(\PDO::FETCH_ASSOC);
            
            $contact_number = $clientData['clt_contact'] ?? '';
            $email = $clientData['clt_email_address'] ?? '';
            
            // Check form type
            $form_type = sanitize('form_type');
            error_log("Form type: " . $form_type);
            
            // Initialize pet_code variable
            $pet_code = null;
            
            // Process depending on form type
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
                $stmt = $conn->prepare("SELECT pet_name, pet_type FROM pet WHERE pet_code = :pet_code");
                $stmt->execute([':pet_code' => $pet_code]);
                $petData = $stmt->fetch(\PDO::FETCH_ASSOC);
                
                $pet_name = $petData['pet_name'] ?? '';
                $pet_type = $petData['pet_type'] ?? '';
                
                // Validation
                if (!$pet_code) $errors[] = "Please select a pet.";
                
            } elseif ($form_type === 'new_pet') {
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
                        $stmt = $conn->prepare('
                            SELECT COUNT(*) as count 
                            FROM pet 
                            WHERE client_code = ? AND LOWER(pet_name) = LOWER(?) AND LOWER(pet_type) = LOWER(?)
                        ');
                        $stmt->execute([$client_code, $pet_name, $pet_type]);
                        $result = $stmt->fetch(\PDO::FETCH_ASSOC);
                        
                        if ($result['count'] > 0) {
                            $errors[] = "You already have a pet with this name and type";
                        } else {
                            // Add the pet
                            error_log("Inserting new pet: $pet_name, $pet_type, $pet_breed, $pet_age");
                            $stmt = $conn->prepare('
                                INSERT INTO pet (client_code, pet_name, pet_type, pet_breed, pet_age, pet_med_history)
                                VALUES (?, ?, ?, ?, ?, ?)
                                RETURNING pet_code
                            ');
                            
                            $success = $stmt->execute([$client_code, $pet_name, $pet_type, $pet_breed, $pet_age, $pet_med_history]);
                            
                            if (!$success) {
                                error_log("Failed to insert pet: " . print_r($stmt->errorInfo(), true));
                                $errors[] = "Failed to add pet. Database error.";
                            } else {
                                $pet_code = $stmt->fetchColumn();
                                error_log("New pet added with ID: $pet_code");
                                
                                if (!$pet_code) {
                                    error_log("Pet was added but no pet_code was returned");
                                    $errors[] = "Failed to retrieve pet ID. Please try again.";
                                }
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
            $hour = (int)substr($preferred_time, 0, 2);
            if ($hour < 9 || $hour > 16) {
                $errors[] = "Appointment time must be between 9:00 AM and 5:00 PM.";
            }
            
            error_log("Validation errors: " . print_r($errors, true));
            
            // Proceed if valid
            if (empty($errors)) {
                try {
                    // Check availability first (server-side)
                    $stmt = $conn->prepare("SELECT COUNT(*) FROM appointment WHERE preferred_date = :date AND preferred_time = :time");
                    $stmt->execute([':date' => $preferred_date, ':time' => $preferred_time]);
                    $count = $stmt->fetchColumn();
                    
                    $max_appointments = 3;
                    if ($count >= $max_appointments) {
                        $errors[] = "Sorry, the selected time slot is already full. Please choose another.";
                    } else {
                        // Save appointment & get ID
                        error_log("Saving appointment for pet_code: $pet_code");
                        $stmt = $conn->prepare("INSERT INTO appointment 
                            (owner_name, contact_number, email, client_code, pet_code, pet_name, pet_type, service, preferred_date, preferred_time, appointment_type, additional_notes, created_at) 
                            VALUES (:owner_name, :contact_number, :email, :client_code, :pet_code, :pet_name, :pet_type, :service, :preferred_date, :preferred_time, :appointment_type, :additional_notes, NOW())
                            RETURNING id");
                            
                        $params = [
                            ':owner_name' => $owner_name,
                            ':contact_number' => $contact_number,
                            ':email' => $email,
                            ':client_code' => $client_code,
                            ':pet_code' => $pet_code,
                            ':pet_name' => $pet_name,
                            ':pet_type' => $pet_type,
                            ':service' => $service,
                            ':preferred_date' => $preferred_date,
                            ':preferred_time' => $preferred_time,
                            ':appointment_type' => $appointment_type,
                            ':additional_notes' => $additional_notes
                        ];
                        
                        error_log("Appointment params: " . print_r($params, true));
                        $stmt->execute($params);
                        
                        $appointment_id = $stmt->fetchColumn();
                        error_log("Appointment created with ID: $appointment_id");
                        
                        // Send confirmation email (client + admin)
                        function sendEmail($to, $subject, $message) {
                            $headers = "MIME-Version: 1.0\r\n";
                            $headers .= "Content-type: text/html; charset=UTF-8\r\n";
                            $headers .= "From: oliverecta13@gmail.com\r\n";
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
                        
                        // Attempt to send emails, but continue if they fail
                        try {
                            sendEmail($email, $user_subject, $user_message);
                            sendEmail("oliverecta13@email.com", $admin_subject, $admin_message);
                        } catch (\Exception $e) {
                            error_log("Error sending email: " . $e->getMessage());
                        }
                        
                        // Set success session
                        if (session_status() == PHP_SESSION_NONE) session_start();
                        $_SESSION['appointment_success'] = true;
                        $_SESSION['appointment_id'] = $appointment_id;
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
                $sql = "SELECT * FROM pet WHERE client_code = ?";
                $stmt = $conn->prepare($sql);
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
            
            // Redirect back to appointment page
            header('Location: /appointment');
            exit;
        }
    }
    // grooming page
    public function grooming() {
        $this->render('home/grooming');
    }
    // confinement page
    public function confinement() {
        $this->render('home/confinement');
    }
    // Show add pet form
    public function showAddPetForm() {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            // Redirect to login page if not logged in
            $_SESSION['redirect_after_login'] = '/add-pet';
            header('Location: /login');
            exit;
        }
        
        // Get user data
        $user = $_SESSION['user'];
        
        // Only clients can add pets
        if ($user['role'] === 'admin') {
            header('Location: /admin/patients');
            exit;
        }
        
        $this->render('home/add_pet', ['user' => $user]);
    }
    
    // Process add pet form
    public function addPet() {
        // Check if user is logged in
        session_start();
        if (!isset($_SESSION['user']) || empty($_SESSION['user'])) {
            header('Location: /login');
            exit;
        }
        
        // Get user data
        $user = $_SESSION['user'];
        
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
                // Get PDO connection
                $pdo = Database::getInstance()->getConnection();
                
                // Get patient model
                $patientModel = new PatientModel($pdo);
                
                // Check for duplicate pet
                if ($patientModel->isDuplicatePet($user['client_code'], $pet_name, $pet_type)) {
                    $errors[] = "You already have a pet with this name and type";
                } else {
                    // Add the pet
                    $success = $patientModel->addPatient(
                        $user['client_code'],
                        $pet_name,
                        $pet_type,
                        $pet_breed,
                        $pet_age,
                        $pet_med_history
                    );
                    
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
}
