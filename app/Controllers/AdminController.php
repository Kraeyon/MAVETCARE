<?php

namespace App\Controllers;

use App\Models\DoctorModel;
use App\Models\PatientModel;
use Config\Database;

class AdminController extends BaseController{
    // para rani makakita sa sidebar og navbar
    public function index() {
        $this->render('admin/index');
    }

    public function patients() {
        // Get the PDO connection
        $pdo = Database::getInstance()->getConnection();

        // Instantiate the PatientModel and pass the connection
        $model = new PatientModel($pdo);
        
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            // Search functionality
            $patients = $model->searchPatients($_GET['search']);
        } else if (isset($_GET['sort']) && isset($_GET['order'])) {
            // Sorting functionality
            $patients = $model->getSortedPatients($_GET['sort'], $_GET['order']);
        } else {
            // Default view
            $patients = $model->getAllPatients();
        }
        
        $clients = $model->getAllClients();
        
        // Render the view and pass data
        $this->render('admin/patients', ['patients' => $patients, 'clients' => $clients]);
    }


    public function appointment() {
        $this->render('admin/appointment');
    }
    public function doctor() {
        $doctorModel = new DoctorModel();
        
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            // Search functionality
            $data['doctors'] = $doctorModel->searchDoctors($_GET['search']);
            $data['search_term'] = $_GET['search'];
        } else {
            // Default view
            $data['doctors'] = $doctorModel->getDoctors();
        }
        
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
