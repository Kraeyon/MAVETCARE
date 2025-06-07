<?php
namespace App\Controllers;

use Config\Database;
use App\Models\PatientModel;
use App\Models\UserModel;

class PatientController {
    private $model;
    private $userModel;

    public function __construct() {
        $this->model = new PatientModel(Database::getInstance()->getConnection());
        $this->userModel = new UserModel();
    }

    // Add a new patient (called from POST /admin/patients/add)
    public function addPatient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $clientCode = $_POST['client_code'];
            $tempPassword = null;

            if ($clientCode === 'new') {
                // Use 'password' as the default temporary password
                $temporaryPassword = 'password';
                
                // Save the password to display to admin
                $tempPassword = $temporaryPassword;
                
                // Prepare user data for registration
                $userData = [
                    'first_name' => $_POST['new_fname'],
                    'last_name' => $_POST['new_lname'],
                    'middle_initial' => $_POST['new_initial'],
                    'contact' => $_POST['new_contact'],
                    'email' => $_POST['new_email'],
                    'address' => $_POST['new_address'],
                    'password' => $temporaryPassword
                ];
                
                // Register client (creates both client and user records)
                $this->userModel->registerClient($userData);
                
                // Get the client code of the newly created client
                $clientCode = $this->model->getClientCodeByEmail($_POST['new_email']);
                
                // Flag to indicate a new client was created
                $newClientCreated = true;
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

            $successUrl = '/admin/patients';
            if ($result) {
                $successUrl .= '?success=added';
                if (isset($newClientCreated) && $newClientCreated) {
                    $successUrl .= '&new_client=yes';
                    if ($tempPassword) {
                        $successUrl .= '&temp_password=' . urlencode($tempPassword);
                    }
                }
            } else {
                $successUrl .= '?error=add_failed';
            }
            
            header('Location: ' . $successUrl);
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
