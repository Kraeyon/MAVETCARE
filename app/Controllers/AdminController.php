<?php

namespace App\Controllers;

use App\Models\DoctorModel;
use App\Models\PatientModel;
use App\Models\TransactionModel;
use App\Models\InventoryModel;
use App\Models\ReviewModel;
use App\Models\AppointmentModel;
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
        // Get the database connection
        $db = Database::getInstance()->getConnection();
        
        // Create the AdminAppointmentModel
        $adminAppointmentModel = new \App\Models\AdminAppointmentModel($db);
        
        // Create the controller instance for use in the view
        $controller = new \App\Controllers\AdminAppointmentController($adminAppointmentModel);
        
        // Render the appointment view and pass the controller
        $this->render('admin/appointment', ['controller' => $controller]);
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

    /**
     * View and manage transactions
     */
    public function transactions() {
        $db = Database::getInstance()->getConnection();
        
        // Filter by payment method (unpaid, etc.) if specified
        $paymentMethod = isset($_GET['payment_method']) ? $_GET['payment_method'] : null;
        
        // Create or get a TransactionModel
        // $transactionModel = new TransactionModel($db);
        
        // For now, we'll just mock this functionality with a simple query
        if ($paymentMethod === 'pending' || $paymentMethod === 'unpaid') {
            $stmt = $db->query("
                SELECT st.*, c.clt_fname, c.clt_lname
                FROM sales_transaction st
                LEFT JOIN client c ON st.client_code = c.clt_code
                WHERE st.transaction_pay_method = 'pending' OR st.transaction_pay_method IS NULL
                ORDER BY st.transaction_datetime DESC
            ");
            $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } else {
            $stmt = $db->query("
                SELECT st.*, c.clt_fname, c.clt_lname 
                FROM sales_transaction st
                LEFT JOIN client c ON st.client_code = c.clt_code
                ORDER BY st.transaction_datetime DESC
            ");
            $transactions = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        }
        
        $this->render('admin/transactions', [
            'transactions' => $transactions,
            'filter' => $paymentMethod
        ]);
    }
    
    /**
     * View appointments with filters
     */
    public function viewAppointmentsFiltered() {
        $db = Database::getInstance()->getConnection();
        $model = new AppointmentModel();
        
        // Get filters from query string
        $status = isset($_GET['status']) ? $_GET['status'] : null;
        $period = isset($_GET['period']) ? $_GET['period'] : null;
        $date = isset($_GET['date']) ? $_GET['date'] : date('Y-m-d');
        
        // Debug information
        error_log("Filtering appointments with status: " . ($status ?? 'none') . ", period: " . ($period ?? 'none') . ", date: " . $date);
        
        // Build query based on filters
        if ($status) {
            // Normalize status parameter and make case-insensitive
            $normalizedStatus = strtolower(trim($status));
            error_log("Normalized status filter: " . $normalizedStatus);
            
            // Map normalized status to expected database values if needed
            $statusMap = [
                'pending' => 'PENDING',
                'confirmed' => 'CONFIRMED',
                'completed' => 'COMPLETED',
                'cancelled' => 'CANCELLED'
            ];
            
            // Use mapped value if available, otherwise use normalized value
            $dbStatus = isset($statusMap[$normalizedStatus]) ? $statusMap[$normalizedStatus] : strtoupper($normalizedStatus);
            
            // Get appointments with the specified status
            $appointments = $model->getAppointmentsByStatus($dbStatus);
            $filterTitle = ucfirst($normalizedStatus) . ' Appointments';
            
            error_log("Found " . count($appointments) . " appointments with status: " . $dbStatus);
        } else if ($period === 'next-week') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+7 days'));
            $appointments = $model->getAppointmentsByDateRange($startDate, $endDate);
            $filterTitle = 'Upcoming Appointments (Next 7 Days)';
        } else if ($date) {
            $appointments = $model->getAppointmentsByDate($date);
            $filterTitle = 'Appointments for ' . date('F j, Y', strtotime($date));
        } else {
            // Default to all appointments
            $stmt = $db->query("
                SELECT a.*, c.clt_fname, c.clt_lname, c.clt_contact, c.clt_email_address,
                       p.pet_name, p.pet_type, p.pet_breed, s.service_name
                FROM appointment a
                JOIN client c ON a.client_code = c.clt_code
                JOIN pet p ON a.pet_code = p.pet_code
                LEFT JOIN service s ON a.service_code = s.service_code
                ORDER BY a.preferred_date DESC, a.preferred_time DESC
            ");
            $appointments = $stmt->fetchAll(PDO::FETCH_ASSOC);
            $filterTitle = 'All Appointments';
        }
        
        $this->render('admin/appointments', [
            'appointments' => $appointments,
            'filterTitle' => $filterTitle,
            'date' => $date,
            'status' => $status,
            'period' => $period
        ]);
    }
    
    /**
     * View inventory with filters
     */
    public function inventoryFiltered() {
        $db = Database::getInstance()->getConnection();
        
        // Get filter from query string
        $filter = isset($_GET['filter']) ? $_GET['filter'] : null;
        
        // Query products based on filter
        if ($filter === 'low-stock') {
            $stmt = $db->query("
                SELECT p.*, s.supp_name 
                FROM product p
                LEFT JOIN supplier s ON p.supp_code = s.supp_code
                WHERE p.prod_stock < 10
                ORDER BY p.prod_stock ASC
            ");
            $filterTitle = 'Low Stock Products';
        } else {
            $stmt = $db->query("
                SELECT p.*, s.supp_name 
                FROM product p
                LEFT JOIN supplier s ON p.supp_code = s.supp_code
                ORDER BY p.prod_name ASC
            ");
            $filterTitle = 'All Products';
        }
        
        $products = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        $this->render('admin/inventory', [
            'products' => $products,
            'filterTitle' => $filterTitle,
            'filter' => $filter
        ]);
    }
    
    /**
     * View client reviews
     */
    public function reviews() {
        $db = Database::getInstance()->getConnection();
        
        // Get recent reviews (last 3 days by default)
        $daysAgo = isset($_GET['days']) ? (int)$_GET['days'] : 3;
        $recentDate = date('Y-m-d', strtotime("-{$daysAgo} days"));
        
        $stmt = $db->prepare("
            SELECT r.*, c.clt_fname, c.clt_lname 
            FROM review r
            JOIN client c ON r.client_code = c.clt_code
            WHERE r.review_date >= ?
            ORDER BY r.review_date DESC
        ");
        $stmt->execute([$recentDate]);
        $reviews = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Get overall rating
        $ratingStmt = $db->query("SELECT AVG(rating) as avg_rating FROM review");
        $avgRating = $ratingStmt->fetch(\PDO::FETCH_ASSOC)['avg_rating'];
        
        $this->render('admin/reviews', [
            'reviews' => $reviews,
            'daysAgo' => $daysAgo,
            'avgRating' => $avgRating
        ]);
    }
    
    /**
     * View all notifications in one page
     */
    public function allNotifications() {
        $db = Database::getInstance()->getConnection();
        $today = date('Y-m-d');
        $nextWeek = date('Y-m-d', strtotime('+7 days'));
        $recentDate = date('Y-m-d', strtotime('-3 days'));
        
        // Get unpaid transactions
        $unpaidStmt = $db->query("
            SELECT st.*, c.clt_fname, c.clt_lname
            FROM sales_transaction st
            LEFT JOIN client c ON st.client_code = c.clt_code
            WHERE st.transaction_pay_method = 'pending' OR st.transaction_pay_method IS NULL
            ORDER BY st.transaction_datetime DESC
            LIMIT 10
        ");
        $unpaidTransactions = $unpaidStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Get pending appointments
        $pendingStmt = $db->prepare("
            SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, s.service_name
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
            WHERE UPPER(a.status) = 'PENDING' AND DATE(a.preferred_date) >= ?
            ORDER BY a.preferred_date ASC, a.preferred_time ASC
            LIMIT 10
        ");
        $pendingStmt->execute([$today]);
        $pendingAppointments = $pendingStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Get low stock products
        $lowStockStmt = $db->query("
            SELECT p.*, s.supp_name 
            FROM product p
            LEFT JOIN supplier s ON p.supp_code = s.supp_code
            WHERE p.prod_stock < 10
            ORDER BY p.prod_stock ASC
            LIMIT 10
        ");
        $lowStockProducts = $lowStockStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Get upcoming appointments
        $upcomingStmt = $db->prepare("
            SELECT a.*, c.clt_fname, c.clt_lname, p.pet_name, p.pet_type, s.service_name
            FROM appointment a
            JOIN client c ON a.client_code = c.clt_code
            JOIN pet p ON a.pet_code = p.pet_code
            LEFT JOIN service s ON a.service_code = s.service_code
            WHERE a.preferred_date BETWEEN ? AND ? AND UPPER(a.status) = 'CONFIRMED'
            ORDER BY a.preferred_date ASC, a.preferred_time ASC
            LIMIT 10
        ");
        $upcomingStmt->execute([$today, $nextWeek]);
        $upcomingAppointments = $upcomingStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Get recent reviews
        $reviewsStmt = $db->prepare("
            SELECT r.*, c.clt_fname, c.clt_lname 
            FROM review r
            JOIN client c ON r.client_code = c.clt_code
            WHERE r.review_date >= ?
            ORDER BY r.review_date DESC
            LIMIT 10
        ");
        $reviewsStmt->execute([$recentDate]);
        $recentReviews = $reviewsStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Debug output
        error_log("Pending appointments count: " . count($pendingAppointments));
        error_log("Upcoming appointments count: " . count($upcomingAppointments));
        
        $this->render('admin/all_notifications', [
            'unpaidTransactions' => $unpaidTransactions,
            'pendingAppointments' => $pendingAppointments,
            'lowStockProducts' => $lowStockProducts,
            'upcomingAppointments' => $upcomingAppointments,
            'recentReviews' => $recentReviews
        ]);
    }

    public function employees() {
        $this->render('admin/employees');
    }

    // --- STAFF MANAGEMENT ---
    public function addEmployee() {
        $pdo = \Config\Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $staff_name = $_POST['staff_name'];
            $staff_position = $_POST['staff_position'];
            $staff_contact = $_POST['staff_contact'];
            $staff_email = $_POST['staff_email'];
            $copy_schedule = isset($_POST['copy_schedule']) ? (int)$_POST['copy_schedule'] : null;
            $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
            try {
                $pdo->beginTransaction();
                // Insert staff
                $stmt = $pdo->prepare("INSERT INTO veterinary_staff (staff_name, staff_position, staff_contact, staff_email_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$staff_name, $staff_position, $staff_contact, $staff_email]);
                $staff_code = $pdo->lastInsertId();
                // Handle schedule
                if ($copy_schedule) {
                    // Copy schedule from another staff
                    $copyStmt = $pdo->prepare("SELECT day_of_week, start_time, end_time FROM staff_schedule WHERE staff_code = ?");
                    $copyStmt->execute([$copy_schedule]);
                    $schedules = $copyStmt->fetchAll(\PDO::FETCH_ASSOC);
                    foreach ($schedules as $sched) {
                        $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                        $stmt->execute([$staff_code, $sched['day_of_week'], $sched['start_time'], $sched['end_time']]);
                    }
                } else if (isset($_POST['schedule'])) {
                    // Use provided schedule
                    foreach ($days as $day) {
                        if (isset($_POST['schedule'][$day]['active'])) {
                            $start_time = $_POST['schedule'][$day]['start_time'] ?: '09:00';
                            $end_time = $_POST['schedule'][$day]['end_time'] ?: '17:00';
                            $stmt = $pdo->prepare("INSERT INTO staff_schedule (staff_code, day_of_week, start_time, end_time) VALUES (?, ?, ?, ?)");
                            $stmt->execute([$staff_code, $day, $start_time, $end_time]);
                        }
                    }
                } else {
                    // Default schedule: Mon-Fri 09:00-17:00
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
        $pdo = \Config\Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $staff_code = $_POST['staff_code'];
            $staff_name = $_POST['staff_name'];
            $staff_position = $_POST['staff_position'];
            $staff_contact = $_POST['staff_contact'];
            $staff_email = $_POST['staff_email'];
            $days = ['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday','Sunday'];
            try {
                $pdo->beginTransaction();
                // Update staff
                $stmt = $pdo->prepare("UPDATE veterinary_staff SET staff_name=?, staff_position=?, staff_contact=?, staff_email_address=? WHERE staff_code=?");
                $stmt->execute([$staff_name, $staff_position, $staff_contact, $staff_email, $staff_code]);
                // Delete old schedule
                $stmt = $pdo->prepare("DELETE FROM staff_schedule WHERE staff_code=?");
                $stmt->execute([$staff_code]);
                // Insert new schedule
                if (isset($_POST['schedule'])) {
                    foreach ($days as $day) {
                        if (isset($_POST['schedule'][$day]['active'])) {
                            $start_time = $_POST['schedule'][$day]['start_time'] ?: '09:00';
                            $end_time = $_POST['schedule'][$day]['end_time'] ?: '17:00';
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
        $pdo = \Config\Database::getInstance()->getConnection();
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

    // --- INVENTORY MANAGEMENT ---
    public function addProduct() {
        $pdo = \Config\Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prod_name = $_POST['prod_name'];
            $prod_category = $_POST['prod_category'];
            $prod_price = $_POST['prod_price'];
            $prod_stock = $_POST['prod_stock'];
            $supp_code = $_POST['supp_code'] ?: null;

            // Only process image if one was uploaded
            if (isset($_FILES['prod_image']) && $_FILES['prod_image']['error'] == 0) {
                $image = $_FILES['prod_image'];
                $imageName = time() . '_' . basename($image['name']);
                $uploadDir = __DIR__ . '/../../public/assets/images/products/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                $uploadPath = $uploadDir . $imageName;
                $dbImagePath = '/assets/images/products/' . $imageName;
                $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
                if (!in_array($image['type'], $allowedTypes)) {
                    die("Invalid image type. Only JPEG, PNG, and GIF are allowed.");
                }
                if ($image['size'] > 5 * 1024 * 1024) {
                    die("Image size too large. Maximum size is 5MB.");
                }
                if (!move_uploaded_file($image['tmp_name'], $uploadPath)) {
                    die("Image upload failed. Check directory permissions.");
                }
            } else {
                $dbImagePath = '/assets/images/products/default.png';
            }
            try {
                $stmt = $pdo->prepare("INSERT INTO product (prod_name, prod_category, prod_price, prod_stock, prod_image, supp_code) VALUES (?, ?, ?, ?, ?, ?)");
                $stmt->execute([$prod_name, $prod_category, $prod_price, $prod_stock, $dbImagePath, $supp_code]);
                header("Location: /admin/inventory?added=1");
                exit;
            } catch (\PDOException $e) {
                die("Database error: " . $e->getMessage());
            }
        }
    }

    public function updateProduct() {
        $pdo = \Config\Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prod_code = $_POST['prod_code'];
            $prod_name = $_POST['prod_name'];
            $prod_category = $_POST['prod_category'];
            $prod_price = $_POST['prod_price'];
            $prod_stock = $_POST['prod_stock'];
            $supp_code = $_POST['supp_code'] ?: null;
            // Image update not handled here for simplicity
            try {
                $stmt = $pdo->prepare("UPDATE product SET prod_name = ?, prod_category = ?, prod_price = ?, prod_stock = ?, supp_code = ? WHERE prod_code = ?");
                $stmt->execute([$prod_name, $prod_category, $prod_price, $prod_stock, $supp_code, $prod_code]);
                header("Location: /admin/inventory?updated=1");
                exit;
            } catch (\PDOException $e) {
                die("Database error: " . $e->getMessage());
            }
        }
    }

    public function deleteProduct() {
        $pdo = \Config\Database::getInstance()->getConnection();
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $prod_code = $_POST['prod_code'] ?? null;
            if (!$prod_code) {
                header("Location: /admin/inventory?error=no_product_code");
                exit;
            }
            try {
                $pdo->beginTransaction();
                
                // First check if product exists
                $checkStmt = $pdo->prepare("SELECT prod_code FROM product WHERE prod_code = ?");
                $checkStmt->execute([$prod_code]);
                if (!$checkStmt->fetch()) {
                    throw new \Exception("Product not found");
                }
                
                // Delete the product
                $stmt = $pdo->prepare("DELETE FROM product WHERE prod_code = ?");
                $stmt->execute([$prod_code]);
                
                $pdo->commit();
                header("Location: /admin/inventory?deleted=1");
                exit;
            } catch (\Exception $e) {
                $pdo->rollBack();
                error_log("Error deleting product: " . $e->getMessage());
                header("Location: /admin/inventory?error=delete_failed");
                exit;
            }
        } else {
            header("Location: /admin/inventory?error=invalid_method");
            exit;
        }
    }
}
