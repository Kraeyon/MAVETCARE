<?php
namespace App\Controllers;

use Config\Database;
use App\Models\PatientModel;

class PatientController {
    private $model;

    public function __construct() {
        $this->model = new PatientModel(Database::getInstance()->getConnection());
    }

    // Add a new patient (called from POST /admin/patients/add)
    public function addPatient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientCode = $_POST['client_code'];

            if ($clientCode === 'new') {
                $clientCode = $this->model->addClient(
                    $_POST['new_fname'],
                    $_POST['new_lname'],
                    $_POST['new_initial'],
                    $_POST['new_contact'],
                    $_POST['new_email'],
                    $_POST['new_address']
                );
            }

            // Check if a pet with the same name, owner, and type already exists
            if ($this->model->isDuplicatePet($clientCode, $_POST['pet_name'], $_POST['pet_type'])) {
                header('Location: /admin/patients?error=duplicate_pet');
                exit();
            }

            $result = $this->model->addPatient(
                $clientCode,
                $_POST['pet_name'],
                $_POST['pet_type'],
                $_POST['pet_breed'],
                $_POST['pet_age'],
                $_POST['pet_med_history']
            );

            header('Location: /admin/patients' . ($result ? '?success=added' : '?error=add_failed'));
            exit();
        }
    }

    // Update existing patient (from POST /admin/patients/update)
    public function updatePatient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Make sure we have all required fields
            if (!isset($_POST['pet_code']) || !isset($_POST['pet_age'])) {
                // Log the error and redirect
                error_log("Missing required fields for pet update");
                header('Location: /admin/patients?error=missing_fields');
                exit();
            }
            
            // Get medical history (might be empty)
            $medHistory = isset($_POST['pet_med_history']) ? $_POST['pet_med_history'] : '';
            
            // Log the values we're using for debugging
            error_log("Updating pet: " . $_POST['pet_code'] . " - Age: " . $_POST['pet_age']);
            
            // Perform the update - only update age and medical history
            $result = $this->model->updatePatientAgeAndHistory(
                $_POST['pet_code'],
                $_POST['pet_age'],
                $medHistory
            );
            
            // Log the result
            error_log("Update result: " . ($result ? 'success' : 'failed'));

            // Redirect back to the patients page
            header('Location: /admin/patients' . ($result ? '?success=updated' : '?error=update_failed'));
            exit();
        }
    }

    // Delete patient (from GET /admin/patients/delete/[i:id])
    public function deletePatient($id) {
        $this->model->deletePatient($id);
        header('Location: /admin/patients');
        exit();
    }
}
