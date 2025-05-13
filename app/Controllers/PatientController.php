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

            $this->model->addPatient(
                $clientCode,
                $_POST['pet_name'],
                $_POST['pet_type'],
                $_POST['pet_breed'],
                $_POST['pet_age'],
                $_POST['pet_med_history']
            );

            header('Location: /admin/patients');
            exit();
        }
    }

    // Update existing patient (from POST /admin/patients/update)
    public function updatePatient() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->model->updatePatient(
                $_POST['pet_code'],
                $_POST['pet_name'],
                $_POST['pet_type'],
                $_POST['pet_breed'],
                $_POST['pet_age'],
                $_POST['pet_med_history']
            );

            header('Location: /admin/patients');
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
