<?php
namespace App\Controllers;

use Config\Database;
use App\Models\PatientModel;

class PatientController {
    private $model;

    public function __construct() {
        $this->model = new PatientModel(Database::getInstance()->getConnection());
    }

    public function listPatients() {
        return $this->model->getAllPatients();
    }

    public function addPatient($data) {
        return $this->model->addPatient($data['client_code'], $data['pet_name'], $data['pet_type'], $data['pet_breed'], $data['pet_age'], $data['pet_med_history']);
    }

    public function updatePatient($data) {
        return $this->model->updatePatient($data['pet_code'], $data['pet_name'], $data['pet_type'], $data['pet_breed'], $data['pet_age'], $data['pet_med_history']);
    }

    public function deletePatient($pet_code) {
        return $this->model->deletePatient($pet_code);
    }
}
