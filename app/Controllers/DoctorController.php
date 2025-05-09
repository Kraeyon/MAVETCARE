<?php

namespace App\Controllers;

use App\Models\DoctorModel;

class DoctorController {
    private $doctorModel;

    public function __construct() {
        $this->doctorModel = new DoctorModel();
    }

    public function index() {
        $doctors = $this->doctorModel->getAllDoctors();
        include __DIR__ . '/../views/admin/doctor.php';
    }
}
