<?php

namespace App\Controllers;

use Config\Database;

class AdminEmployeeController extends BaseController {
    public function index() {
        $this->render('admin/employees');
    }

    public function addEmployee() {
        $pdo = Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $staff_name = $_POST['staff_name'];
            $staff_position = $_POST['staff_position'];
            $staff_contact = $_POST['staff_contact'];
            $staff_email = $_POST['staff_email'];
            $copy_schedule = isset($_POST['copy_schedule']) ? (int)$_POST['copy_schedule'] : null;
            $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("INSERT INTO veterinary_staff (staff_name, staff_position, staff_contact, staff_email_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$staff_name, $staff_position, $staff_contact, $staff_email]);
                $staff_code = $pdo->lastInsertId();
                if ($copy_schedule) {
                    $copyStmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM staff_schedule WHERE staff_code = ?");
                    $copyStmt->execute([$copy_schedule]);
                    $schedules = $copyStmt->fetchAll(\PDO::FETCH_ASSOC);
                    foreach ($schedules as $sched) {
                        $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$staff_code, $sched['day_of_week'], $sched['start_time'], $sched['end_time']]);
                    }
                } else if (isset($_POST['schedule'])) {
                    foreach ($days as $day) {
                        if (isset($_POST['schedule'][$day]['active'])) {
                            $start_time = $_POST['schedule'][$day]['start_time'] ?: '09:00';
                            $end_time = $_POST['schedule'][$day]['end_time'] ?: '17:00';
                            $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$staff_code, $day, $start_time, $end_time]);
                        }
                    }
                } else {
                    foreach (['Monday','Tuesday','Wednesday','Thursday','Friday'] as $day) {
                        $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$staff_code, $day, '09:00', '17:00']);
                    }
                }
                $pdo->commit();
                header("Location: /admin/employees?added=1");
                exit;
            } catch (\PDOException $e) {
                $pdo->rollBack();
                die("Database error: " . $e->getMessage());
            }
        }
    }

    public function editEmployee() {
        $pdo = Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $staff_code = $_POST['staff_code'];
            $staff_name = $_POST['staff_name'];
            $staff_position = $_POST['staff_position'];
            $staff_contact = $_POST['staff_contact'];
            $staff_email = $_POST['staff_email'];
            $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
            
            try {
                $pdo->beginTransaction();
                
                // Update staff information
                $stmt = $pdo->prepare("UPDATE veterinary_staff SET staff_name=?, staff_position=?, staff_contact=?, staff_email_address=? WHERE staff_code=?");
                $stmt->execute([$staff_name, $staff_position, $staff_contact, $staff_email, $staff_code]);
                
                // Delete existing schedule
                $stmt = $pdo->prepare("DELETE FROM staff_schedule WHERE staff_code=?");
                $stmt->execute([$staff_code]);
                
                // Insert new schedule
                if (isset($_POST['schedule'])) {
                    foreach ($days as $day) {
                        if (isset($_POST['schedule'][$day]['active']) && 
                            !empty($_POST['schedule'][$day]['start_time']) && 
                            !empty($_POST['schedule'][$day]['end_time'])) {
                            
                            $start_time = $_POST['schedule'][$day]['start_time'];
                            $end_time = $_POST['schedule'][$day]['end_time'];
                            
                            $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$staff_code, $day, $start_time, $end_time]);
                        }
                    }
                }
                
                $pdo->commit();
                header("Location: /admin/employees?updated=1");
                exit;
            } catch (\PDOException $e) {
                $pdo->rollBack();
                die("Database error: " . $e->getMessage());
            }
        }
    }

    public function deleteEmployee() {
        $pdo = Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $staff_code = $_POST['staff_code'];
            try {
                $pdo->beginTransaction();
                $stmt = $pdo->prepare("DELETE FROM staff_schedule WHERE staff_code=?");
                $stmt->execute([$staff_code]);
                $stmt = $pdo->prepare("DELETE FROM veterinary_staff WHERE staff_code=?");
                $stmt->execute([$staff_code]);
                $pdo->commit();
                header("Location: /admin/employees?deleted=1");
                exit;
            } catch (\PDOException $e) {
                $pdo->rollBack();
                die("Database error: " . $e->getMessage());
            }
        }
    }
} 