<?php

namespace App\Controllers;

use App\Models\DoctorModel;

class AdminController extends BaseController{
    // para rani makakita sa sidebar og navbar
    public function index() {
        $this->render('admin/index');
    }

    public function patients() {
        $this->render('admin/patients');
    }   

    public function appointment() {
        $this->render('admin/appointment');
    }
    public function doctor() {
        $doctorModel = new DoctorModel();
        $data['doctors'] = $doctorModel->getDoctors();
        $this->render('admin/doctor', $data);
    }
    
    public function editDoctorSchedule($staffCode) {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $newSchedule = trim($_POST['schedule']);

            // Call the model method to update the schedule in the database
            $doctorModel = new DoctorModel();
            $doctorModel->updateDoctorSchedule($staffCode, $newSchedule);

            // Redirect back to the doctor list page
            header('Location: /admin/doctor');
            exit;
        }
    }


    public function schedule() {
        $this->render('admin/schedule');
    }

    public function inventory() {
        $this->render('admin/inventory');
    }

    public function employees() {
        $this->render('admin/employees');
    }
}
