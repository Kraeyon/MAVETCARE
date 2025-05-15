<?php
namespace App\Controllers;

use Config\Database;
use App\Models\AdminAppointmentModel;
use App\Utils\StatusHelper;

class AdminAppointmentController {
    public $appointments;
    public $services;
    public $clients;
    public $pets = [];
    public $message = "";
    public $error = "";
    private $adminAppointmentModel;
    private $db;

    public function __construct(AdminAppointmentModel $adminAppointmentModel) {
        $this->adminAppointmentModel = $adminAppointmentModel;
        $this->db = Database::getInstance()->getConnection(); // For direct database operations
        
        // Initialize properties needed by the view
        // Check for search parameters
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'appt_datetime';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        
        // Check if we have search term
        if (!empty($searchTerm)) {
            // If search term is numeric and looks like an ID, try exact ID search first
            if (is_numeric($searchTerm) && $searchTerm > 0) {
                $appointment = $this->adminAppointmentModel->findAppointmentById($searchTerm);
                if ($appointment) {
                    // Found exact match by ID
                    $this->appointments = [$appointment];
                    $this->message = "Found appointment #" . $searchTerm;
                } else {
                    // No exact match, fall back to regular search
                    $this->appointments = $this->adminAppointmentModel->getAppointments($searchTerm, $sort, $order);
                    if (empty($this->appointments)) {
                        $this->error = "No appointments found matching: " . $searchTerm;
                    }
                }
            } else {
                // Regular text search
                $this->appointments = $this->adminAppointmentModel->getAppointments($searchTerm, $sort, $order);
                if (empty($this->appointments)) {
                    $this->error = "No appointments found matching: " . $searchTerm;
                }
            }
        } else {
            // No search term, show all appointments
            $this->appointments = $this->adminAppointmentModel->getAppointments(null, $sort, $order);
        }
        
        $this->services = $this->adminAppointmentModel->getServices();
        $this->clients = $this->adminAppointmentModel->getClients();

        // Process any POST requests
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $this->handlePostRequests();
        }
    }
    
    /**
     * Process any POST requests
     */
    private function handlePostRequests() {
        if (isset($_POST['add_appointment'])) {
            $result = $this->addAppointment($_POST);
            $this->message = $result ? "Appointment added successfully!" : "Error adding appointment";
        }

        if (isset($_POST['update_appointment'])) {
            $result = $this->updateAppointment($_POST);
            $this->message = $result ? "Appointment updated successfully!" : "Error updating appointment";
        }

        if (isset($_POST['delete_appointment'])) {
            $appt_code = $_POST['appt_code'];
            $result = $this->deleteAppointment($appt_code);
            $this->message = $result ? "Appointment deleted successfully!" : "Error deleting appointment";
        }

        // If user selected a client to load pets
        if (isset($_POST['selected_client_id'])) {
            $client_id = $_POST['selected_client_id'];
            $this->pets = $this->adminAppointmentModel->getPetsByClient($client_id);
        }

        // Reload appointments, services, and clients after post
        $searchTerm = isset($_GET['search']) ? trim($_GET['search']) : null;
        $sort = isset($_GET['sort']) ? $_GET['sort'] : 'appt_datetime';
        $order = isset($_GET['order']) ? $_GET['order'] : 'ASC';
        
        // Same search logic as in constructor
        if (!empty($searchTerm)) {
            if (is_numeric($searchTerm) && $searchTerm > 0) {
                $appointment = $this->adminAppointmentModel->findAppointmentById($searchTerm);
                if ($appointment) {
                    $this->appointments = [$appointment];
                } else {
                    $this->appointments = $this->adminAppointmentModel->getAppointments($searchTerm, $sort, $order);
                }
            } else {
                $this->appointments = $this->adminAppointmentModel->getAppointments($searchTerm, $sort, $order);
            }
        } else {
            $this->appointments = $this->adminAppointmentModel->getAppointments(null, $sort, $order);
        }
        
        $this->services = $this->adminAppointmentModel->getServices();
        $this->clients = $this->adminAppointmentModel->getClients();
    }

    /**
     * Search for appointments
     */
    public function searchAppointments($searchTerm) {
        return $this->adminAppointmentModel->searchAppointments($searchTerm);
    }
    
    /**
     * Find appointment by ID
     */
    public function findAppointmentById($id) {
        return $this->adminAppointmentModel->findAppointmentById($id);
    }

    private function addAppointment($data) {
        $appt_datetime = $data['appt_date'] . ' ' . $data['appt_time'];
        return $this->adminAppointmentModel->addAppointment(
            $data['client_code'],
            $data['pet_code'],
            $data['service_code'],
            $appt_datetime,
            $data['appointment_type'],
            $data['status'],
            $data['additional_notes']
        );
    }

    private function updateAppointment($data) {
        $appt_datetime = $data['appt_date'] . ' ' . $data['appt_time'];
        return $this->adminAppointmentModel->updateAppointment(
            $data['appt_code'],
            $data['client_code'],
            $data['pet_code'],
            $data['service_code'],
            $appt_datetime,
            $data['appointment_type'],
            $data['status'],
            $data['additional_notes']
        );
    }

    private function deleteAppointment($appt_code) {
        return $this->adminAppointmentModel->deleteAppointment($appt_code);
    }
    
    /**
     * Update appointment status (API endpoint)
     */
    public function updateAppointmentStatus() {
        // Check if this is an AJAX request
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['appt_code'], $_POST['status'])) {
            $appt_code = $_POST['appt_code'];
            $status = $_POST['status'];
            
            error_log("Attempting to update appointment #$appt_code to status: $status");
            
            // Validate the status using StatusHelper
            if (!StatusHelper::isValidStatus($status)) {
                error_log("Invalid status provided: $status");
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                return;
            }
            
            try {
                // Get the current appointment data
                $appointment = $this->adminAppointmentModel->findAppointmentById($appt_code);
                
                if (!$appointment) {
                    error_log("Appointment #$appt_code not found");
                    echo json_encode(['success' => false, 'message' => 'Appointment not found']);
                    return;
                }
                
                // Update the status only
                $result = $this->adminAppointmentModel->updateAppointment(
                    $appt_code,
                    $appointment['client_code'],
                    $appointment['pet_code'],
                    $appointment['service_code'],
                    $appointment['appt_datetime'],
                    $appointment['appointment_type'],
                    $status, // This will be converted to uppercase in the model
                    $appointment['additional_notes']
                );
                
                error_log("Update result: " . ($result ? "Success" : "Failed"));
                
                if ($result) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Status updated successfully',
                        'new_status' => StatusHelper::getDbStatus($status),
                        'display_status' => StatusHelper::getDisplayStatus($status),
                        'status_class' => StatusHelper::getStatusClass($status)
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
                }
            } catch (\PDOException $e) {
                // Log the error
                error_log("Error updating appointment status: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
            } catch (\Exception $e) {
                // Log any other errors
                error_log("General error updating appointment status: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
            }
        } else {
            error_log("Invalid updateAppointmentStatus request");
            echo json_encode(['success' => false, 'message' => 'Invalid request']);
        }
    }
    
    /**
     * Show form to add a new appointment
     */
    public function showAddForm() {
        // In a real application, you'd likely use a view renderer here
        echo "Add appointment form goes here";
    }
    
    /**
     * Show form to edit an existing appointment
     */
    public function showEditForm($id) {
        // Get the appointment details
        $appointment = $this->adminAppointmentModel->getAppointmentById($id);
        
        if (!$appointment) {
            // Handle not found case
            echo "Appointment not found";
            return;
        }
        
        // Get data needed for the form
        $services = $this->adminAppointmentModel->getServices();
        $clients = $this->adminAppointmentModel->getClients();
        $pets = $this->adminAppointmentModel->getPetsByClient($appointment['client_code']);
        
        // Include the view file
        include '../app/Views/admin/edit_appointment.php';
    }

    /**
     * Update an appointment (form submission handler)
     */
    public function handleAppointmentUpdate() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_appointment'])) {
            // Format date and time
            $apptDatetime = $_POST['appt_date'] . ' ' . $_POST['appt_time'];
            
            // Call model method to update
            $result = $this->adminAppointmentModel->updateAppointment(
                $_POST['appt_code'],
                $_POST['client_code'],
                $_POST['pet_code'],
                $_POST['service_code'],
                $apptDatetime,
                $_POST['appointment_type'],
                $_POST['status'],
                $_POST['additional_notes'] ?? ''
            );
            
            // Redirect back to appointments list with success/error message
            if ($result) {
                header('Location: /admin/appointments?message=Appointment updated successfully');
            } else {
                header('Location: /admin/appointments?error=Failed to update appointment');
            }
            exit;
        }
        
        // Invalid request
        header('Location: /admin/appointments');
        exit;
    }
    
    /**
     * Delete an appointment (form submission handler)
     */
    public function handleAppointmentDelete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_appointment'], $_POST['appt_code'])) {
            $apptCode = $_POST['appt_code'];
            
            // Call model method to delete
            $result = $this->adminAppointmentModel->deleteAppointment($apptCode);
            
            // Redirect back to appointments list with success/error message
            if ($result) {
                header('Location: /admin/appointments?message=Appointment deleted successfully');
            } else {
                header('Location: /admin/appointments?error=Failed to delete appointment');
            }
            exit;
        }
        
        // Invalid request
        header('Location: /admin/appointments');
        exit;
    }
    
    /**
     * Get pets by client (AJAX endpoint)
     */
    public function getPetsByClient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selected_client_id'])) {
            $clientId = $_POST['selected_client_id'];
            
            // Get pets for the selected client
            $pets = $this->adminAppointmentModel->getPetsByClient($clientId);
            
            // Return as JSON
            header('Content-Type: application/json');
            echo json_encode($pets);
            exit;
        }
        
        // Invalid request
        header('Content-Type: application/json');
        echo json_encode([]);
    }
}
?>
