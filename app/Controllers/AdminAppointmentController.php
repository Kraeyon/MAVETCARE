<?php
include __DIR__ . '/../Models/admin_appointment_model.php';

class AdminAppointmentController {
    public $appointments;
    public $services;
    public $clients;
    public $pets = [];
    public $message = "";
    public $error = "";

    public function __construct() {
        global $conn;
        $this->appointments = getAppointments($conn);
        $this->services = getServices($conn);
        $this->clients = getClients($conn);

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['add_appointment'])) {
                $result = addAppointment($conn, $_POST);
                $this->message = $result ? "Appointment added successfully!" : "Error: " . pg_last_error($conn);
            }

            if (isset($_POST['update_appointment'])) {
                $result = updateAppointment($conn, $_POST);
                $this->message = $result ? "Appointment updated successfully!" : "Error: " . pg_last_error($conn);
            }

            if (isset($_POST['delete_appointment'])) {
                $appt_code = pg_escape_string($conn, $_POST['appt_code']);
                $result = deleteAppointment($conn, $appt_code);
                $this->message = $result ? "Appointment deleted successfully!" : "Error: " . pg_last_error($conn);
            }

            // If user selected a client to load pets
            if (isset($_POST['selected_client_id'])) {
                $client_id = $_POST['selected_client_id'];
                $this->pets = getPetsByClient($conn, $client_id);
            }

            $this->appointments = getAppointments($conn);
            $this->services = getServices($conn);
            $this->clients = getClients($conn);
        }
    }
}
?>
