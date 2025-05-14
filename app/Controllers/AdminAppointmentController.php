<?php
namespace App\Controllers;

use Config\Database;
use App\Models\AdminAppointmentModel;

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
        $this->appointments = $this->adminAppointmentModel->getAppointments();
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
        $this->appointments = $this->adminAppointmentModel->getAppointments();
        $this->services = $this->adminAppointmentModel->getServices();
        $this->clients = $this->adminAppointmentModel->getClients();
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
            
            // Validate the status
            $validStatuses = ['pending', 'confirmed', 'completed', 'cancelled'];
            if (!in_array(strtolower($status), $validStatuses)) {
                echo json_encode(['success' => false, 'message' => 'Invalid status']);
                return;
            }
            
            try {
                // Use PDO to update the appointment status
                $sql = "UPDATE appointment SET status = ? WHERE appt_code = ?";
                $stmt = $this->db->prepare($sql);
                $result = $stmt->execute([strtoupper($status), $appt_code]);
                
                if ($result) {
                    echo json_encode(['success' => true]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'Failed to update status']);
                }
            } catch (\PDOException $e) {
                // Log the error
                error_log("Error updating appointment status: " . $e->getMessage());
                echo json_encode(['success' => false, 'message' => 'Database error']);
            }
        } else {
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
