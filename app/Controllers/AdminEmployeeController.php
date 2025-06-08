<?php

namespace App\Controllers;

use Config\Database;

class AdminEmployeeController extends BaseController {
    public function index() {
        $pdo = $this->getPDO();
        
        // Get search term if provided
        $search = isset($_GET['search']) ? trim($_GET['search']) : null;
        
        // Base query
        $baseQuery = "
            SELECT vs.*, 
                (SELECT JSON_AGG(ss.*) FROM staff_schedule ss WHERE ss.staff_code = vs.staff_code) as schedule_details 
            FROM veterinary_staff vs
            WHERE (vs.status = 'ACTIVE' OR vs.status IS NULL)
        ";
        
        // Parameters for prepared statement
        $params = [];
        
        // Add search condition if search term is provided
        if (!empty($search)) {
            // Check if search term is numeric
            $isNumeric = is_numeric($search);
            
            if ($isNumeric) {
                // If numeric, search in text fields and numeric fields
                $baseQuery .= " AND (vs.staff_name LIKE ? OR vs.staff_position LIKE ? OR vs.staff_contact LIKE ? OR vs.staff_email_address LIKE ? OR vs.staff_code = ?)";
                $searchParam = "%$search%";
                $params = [$searchParam, $searchParam, $searchParam, $searchParam, $search];
            } else {
                // If not numeric, only search in text fields
                $baseQuery .= " AND (vs.staff_name LIKE ? OR vs.staff_position LIKE ? OR vs.staff_contact LIKE ? OR vs.staff_email_address LIKE ?)";
                $searchParam = "%$search%";
                $params = [$searchParam, $searchParam, $searchParam, $searchParam];
            }
        }
        
        // Add order by clause
        $baseQuery .= " ORDER BY vs.staff_name";
        
        // Prepare and execute the query
        $stmt = $pdo->prepare($baseQuery);
        $stmt->execute($params);
        $staff = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->render('admin/employees', [
            'staff' => $staff,
            'search' => $search
        ]);
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
                            $start_time = !empty($_POST['schedule'][$day]['start_time']) ? $_POST['schedule'][$day]['start_time'] : '09:00';
                            $end_time = !empty($_POST['schedule'][$day]['end_time']) ? $_POST['schedule'][$day]['end_time'] : '17:00';
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
                        if (isset($_POST['schedule'][$day]['active'])) {
                            // Use default values if time fields are empty
                            $start_time = !empty($_POST['schedule'][$day]['start_time']) ? $_POST['schedule'][$day]['start_time'] : '09:00';
                            $end_time = !empty($_POST['schedule'][$day]['end_time']) ? $_POST['schedule'][$day]['end_time'] : '17:00';
                            
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

    /**
     * Archive an employee by setting status to inactive
     */
    public function archiveEmployee() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff_code'])) {
            $pdo = $this->getPDO();
            $staff_code = filter_var($_POST['staff_code'], FILTER_SANITIZE_NUMBER_INT);
            
            try {
                $pdo->beginTransaction();
                
                // Update staff status to inactive
                $stmt = $pdo->prepare("UPDATE veterinary_staff SET status = 'INACTIVE', updated_at = NOW() WHERE staff_code = ?");
                $stmt->execute([$staff_code]);
                
                $pdo->commit();
                header("Location: /admin/employees?archived=1");
            } catch (\Exception $e) {
                $pdo->rollBack();
                error_log("Error archiving employee: " . $e->getMessage());
                header("Location: /admin/employees?error=archive_failed");
            }
            exit;
        }
        
        header("Location: /admin/employees");
        exit;
    }
    
    /**
     * @deprecated Use archiveEmployee() instead
     */
    public function deleteEmployee() {
        // Redirect to archive method
        $this->archiveEmployee();
    }
} 