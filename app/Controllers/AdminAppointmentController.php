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

    public function __construct(AdminAppointmentModel $adminAppointmentModel) {
        $this->adminAppointmentModel = $adminAppointmentModel;
        $this->appointments = $this->adminAppointmentModel->getAppointments();
        $this->services = $this->adminAppointmentModel->getServices();
        $this->clients = $this->adminAppointmentModel->getClients();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            if (isset($_POST['add_appointment'])) {
                $result = $this->addAppointment($_POST);
                $this->message = $result ? "Appointment added successfully!" : "Error: " . $this->adminAppointmentModel->getDbError();
            }

            if (isset($_POST['update_appointment'])) {
                $result = $this->updateAppointment($_POST);
                $this->message = $result ? "Appointment updated successfully!" : "Error: " . $this->adminAppointmentModel->getDbError();
            }

            if (isset($_POST['delete_appointment'])) {
                $appt_code = $_POST['appt_code'];
                $result = $this->deleteAppointment($appt_code);
                $this->message = $result ? "Appointment deleted successfully!" : "Error: " . $this->adminAppointmentModel->getDbError();
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
    }

    private function addAppointment($data) {
        $appt_datetime = $data['appt_date'] . ' ' . $data['appt_time'];
        return $this->adminAppointmentModel->addAppointment(
            $data['client_code'],
            $data['pet_code'],
            $data['service_code'],
            $appt_datetime,
            $data['appt_type'],
            $data['appt_status'],
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
            $data['appt_type'],
            $data['appt_status'],
            $data['additional_notes']
        );
    }

    private function deleteAppointment($appt_code) {
        return $this->adminAppointmentModel->deleteAppointment($appt_code);
    }
}
?>
