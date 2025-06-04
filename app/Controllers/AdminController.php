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
        
        // Tables have been dropped, show an informational message
        $this->render('admin/error', [
            'title' => 'Transactions Unavailable',
            'message' => 'The transaction feature is currently unavailable as the related database tables have been removed.',
            'back_url' => '/admin'
        ]);
    }
    
    /**
     * View appointments with filters
     */
    public function viewAppointmentsFiltered() {
        // Instead of showing a separate page, redirect to the main appointment dashboard
        // with the appropriate filter parameters
        $queryParams = [];
        
        // Get filters from query string and pass them to the redirect
        if (isset($_GET['status'])) {
            $queryParams['filter'] = strtolower($_GET['status']);
        }
        if (isset($_GET['period']) && $_GET['period'] == 'next-week') {
            $queryParams['filter'] = 'upcoming';
        }
        if (isset($_GET['date'])) {
            $queryParams['date'] = $_GET['date'];
        }
        
        // Build the redirect URL
        $redirectUrl = '/admin/appointment';
        if (!empty($queryParams)) {
            $redirectUrl .= '?' . http_build_query($queryParams);
        }
        
        // Perform the redirect
        header('Location: ' . $redirectUrl);
        exit;
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
                SELECT p.*
                FROM product p
                WHERE p.prod_stock < 10 AND (p.prod_status = 'ACTIVE' OR p.prod_status IS NULL)
                ORDER BY p.prod_stock ASC
            ");
            $filterTitle = 'Low Stock Products';
        } else {
            $stmt = $db->query("
                SELECT p.*
                FROM product p
                WHERE p.prod_status = 'ACTIVE' OR p.prod_status IS NULL
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

    /**
     * Archive a product (mark as inactive) instead of deleting it
     */
    public function archiveProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            try {
                $productId = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
                
                // Check if product exists
                $pdo = $this->getPDO();
                $checkStmt = $pdo->prepare("SELECT prod_code FROM product WHERE prod_code = ?");
                $checkStmt->execute([$productId]);
                
                if (!$checkStmt->fetch()) {
                    header("Location: /admin/inventory?error=product_not_found");
                    exit;
                }
                
                // Archive the product by setting status to inactive
                $stmt = $pdo->prepare("UPDATE product SET prod_status = 'ARCHIVED', updated_at = NOW() WHERE prod_code = ?");
                $success = $stmt->execute([$productId]);
                
                if ($success) {
                    header("Location: /admin/inventory?archived=1");
                } else {
                    header("Location: /admin/inventory?error=archive_failed");
                }
            } catch (\Exception $e) {
                error_log("Error archiving product: " . $e->getMessage());
                header("Location: /admin/inventory?error=archive_failed");
            }
            exit;
        }
        
        header("Location: /admin/inventory");
        exit;
    }
    
    /**
     * @deprecated Use archiveProduct() instead
     */
    public function deleteProduct() {
        // Redirect to archive method
        $this->archiveProduct();
    }

    /**
     * Get archived products
     * 
     * @return array List of archived products
     */
    private function getArchivedProducts() {
        try {
            $pdo = $this->getPDO();
            $stmt = $pdo->prepare("
                SELECT p.*
                FROM product p
                WHERE p.prod_status = 'ARCHIVED'
                ORDER BY p.prod_name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error fetching archived products: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Get archived staff members
     * 
     * @return array List of archived staff
     */
    private function getArchivedStaff() {
        try {
            $pdo = $this->getPDO();
            $stmt = $pdo->prepare("
                SELECT vs.*
                FROM veterinary_staff vs
                WHERE vs.status = 'INACTIVE'
                ORDER BY vs.staff_name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error fetching archived staff: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * View all archived items
     */
    public function archivedItems() {
        // Get archived products
        $archivedProducts = $this->getArchivedProducts();
        
        // Get archived staff
        $archivedStaff = $this->getArchivedStaff();
        
        // Get archived services
        $archivedServices = $this->getArchivedServices();
        
        // Render the view
        $this->render('admin/archived_items', [
            'products' => $archivedProducts,
            'staff' => $archivedStaff,
            'services' => $archivedServices,
            'message' => isset($_GET['service_restored']) ? 'Service restored successfully' : 
                      (isset($_GET['product_restored']) ? 'Product restored successfully' : 
                      (isset($_GET['staff_restored']) ? 'Staff restored successfully' : null)),
            'error' => isset($_GET['error']) ? $_GET['error'] : null
        ]);
    }

    /**
     * Restore an archived product
     */
    public function restoreProduct() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
            try {
                $productId = filter_var($_POST['product_id'], FILTER_SANITIZE_NUMBER_INT);
                $pdo = $this->getPDO();
                
                // Update product status to active
                $stmt = $pdo->prepare("UPDATE product SET prod_status = 'ACTIVE', updated_at = NOW() WHERE prod_code = ? AND prod_status = 'ARCHIVED'");
                $success = $stmt->execute([$productId]);
                
                if ($success) {
                    header("Location: /admin/archived?product_restored=1");
                } else {
                    header("Location: /admin/archived?error=restore_failed");
                }
            } catch (\Exception $e) {
                error_log("Error restoring product: " . $e->getMessage());
                header("Location: /admin/archived?error=restore_failed");
            }
            exit;
        }
        
        header("Location: /admin/archived");
        exit;
    }
    
    /**
     * Restore an archived staff member
     */
    public function restoreStaff() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['staff_code'])) {
            try {
                $staffCode = filter_var($_POST['staff_code'], FILTER_SANITIZE_NUMBER_INT);
                $pdo = $this->getPDO();
                
                // Update staff status to active
                $stmt = $pdo->prepare("UPDATE veterinary_staff SET status = 'ACTIVE', updated_at = NOW() WHERE staff_code = ? AND status = 'INACTIVE'");
                $success = $stmt->execute([$staffCode]);
                
                if ($success) {
                    header("Location: /admin/archived?staff_restored=1");
                } else {
                    header("Location: /admin/archived?error=restore_failed");
                }
            } catch (\Exception $e) {
                error_log("Error restoring staff: " . $e->getMessage());
                header("Location: /admin/archived?error=restore_failed");
            }
            exit;
        }
        
        header("Location: /admin/archived");
        exit;
    }

    /**
     * Show the form to add a new service
     */
    public function showAddServiceForm() {
        try {
            // Fetch existing active services for reference
            $stmt = $this->getPDO()->query("
                SELECT service_code, service_name, service_desc, service_fee, service_img
                FROM service
                WHERE status = 'ACTIVE' OR status IS NULL
                ORDER BY service_name ASC
            ");
            $services = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Render the view with services data
            $this->render('admin/service_add', [
                'services' => $services,
                'message' => isset($_GET['message']) ? $_GET['message'] : null,
                'error' => isset($_GET['error']) ? $_GET['error'] : null
            ]);
        } catch (\PDOException $e) {
            error_log("Error fetching services: " . $e->getMessage());
            $this->render('admin/service_add', [
                'services' => [],
                'error' => 'Database error: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Handle service addition
     */
    public function addService() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serviceName = $_POST['service_name'] ?? '';
            $serviceDesc = $_POST['service_desc'] ?? '';
            $serviceFee = isset($_POST['service_fee']) && $_POST['service_fee'] !== '' ? (float)$_POST['service_fee'] : 0;
            
            if (empty($serviceName)) {
                header('Location: /admin/services/add?error=Service name is required');
                exit;
            }
            
            try {
                // Handle image upload if present
                $serviceImgPath = '/assets/images/services/default.png'; // Default image path
                
                if (isset($_FILES['service_img']) && $_FILES['service_img']['error'] == 0) {
                    $uploadDir = __DIR__ . '/../../public/assets/images/services/';
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0755, true)) {
                            error_log("Failed to create directory: " . $uploadDir);
                            header('Location: /admin/services/add?error=Failed to create image upload directory');
                            exit;
                        }
                    }
                    
                    // Generate unique filename
                    $fileName = uniqid('service_') . '_' . basename($_FILES['service_img']['name']);
                    $uploadPath = $uploadDir . $fileName;
                    
                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($_FILES['service_img']['type'], $allowedTypes)) {
                        header('Location: /admin/services/add?error=Invalid image type. Only JPEG, PNG, GIF, and WebP are allowed');
                        exit;
                    }
                    
                    // Validate file size (max 2MB)
                    if ($_FILES['service_img']['size'] > 2 * 1024 * 1024) {
                        header('Location: /admin/services/add?error=Image is too large. Maximum size is 2MB');
                        exit;
                    }
                    
                    // Move uploaded file
                    if (move_uploaded_file($_FILES['service_img']['tmp_name'], $uploadPath)) {
                        $serviceImgPath = '/assets/images/services/' . $fileName;
                    } else {
                        error_log("Failed to move uploaded file: " . $_FILES['service_img']['tmp_name'] . " to " . $uploadPath);
                    }
                }
                
                // Insert service with image path
                $stmt = $this->getPDO()->prepare("
                    INSERT INTO service (service_name, service_desc, service_fee, service_img)
                    VALUES (?, ?, ?, ?)
                ");
                $result = $stmt->execute([$serviceName, $serviceDesc, $serviceFee, $serviceImgPath]);
                
                if ($result) {
                    header('Location: /admin/services/add?message=Service added successfully');
                } else {
                    header('Location: /admin/services/add?error=Failed to add service');
                }
                exit;
            } catch (\PDOException $e) {
                error_log("Error adding service: " . $e->getMessage());
                header('Location: /admin/services/add?error=' . urlencode('Database error: ' . $e->getMessage()));
                exit;
            } catch (\Exception $e) {
                error_log("General error adding service: " . $e->getMessage());
                header('Location: /admin/services/add?error=' . urlencode('Error: ' . $e->getMessage()));
                exit;
            }
        }
        
        // If not a POST request, redirect back to the form
        header('Location: /admin/services/add');
        exit;
    }
    
    /**
     * Update an existing service
     */
    public function updateService() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $serviceCode = $_POST['service_code'] ?? '';
            $serviceName = $_POST['service_name'] ?? '';
            $serviceDesc = $_POST['service_desc'] ?? '';
            $serviceFee = isset($_POST['service_fee']) && $_POST['service_fee'] !== '' ? (float)$_POST['service_fee'] : 0;
            $currentImgPath = $_POST['current_img_path'] ?? '/assets/images/services/default.png';
            
            if (empty($serviceCode) || empty($serviceName)) {
                header('Location: /admin/services/add?error=Service code and name are required');
                exit;
            }
            
            try {
                // Handle image upload if present
                $serviceImgPath = $currentImgPath; // Default to current image path
                
                if (isset($_FILES['service_img']) && $_FILES['service_img']['error'] == 0) {
                    $uploadDir = __DIR__ . '/../../public/assets/images/services/';
                    
                    // Create directory if it doesn't exist
                    if (!is_dir($uploadDir)) {
                        if (!mkdir($uploadDir, 0755, true)) {
                            error_log("Failed to create directory: " . $uploadDir);
                            header('Location: /admin/services/add?error=Failed to create image upload directory');
                            exit;
                        }
                    }
                    
                    // Generate unique filename
                    $fileName = uniqid('service_') . '_' . basename($_FILES['service_img']['name']);
                    $uploadPath = $uploadDir . $fileName;
                    
                    // Validate file type
                    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
                    if (!in_array($_FILES['service_img']['type'], $allowedTypes)) {
                        header('Location: /admin/services/add?error=Invalid image type. Only JPEG, PNG, GIF, and WebP are allowed');
                        exit;
                    }
                    
                    // Validate file size (max 2MB)
                    if ($_FILES['service_img']['size'] > 2 * 1024 * 1024) {
                        header('Location: /admin/services/add?error=Image is too large. Maximum size is 2MB');
                        exit;
                    }
                    
                    // Move uploaded file
                    if (move_uploaded_file($_FILES['service_img']['tmp_name'], $uploadPath)) {
                        $serviceImgPath = '/assets/images/services/' . $fileName;
                        
                        // Delete previous image if it's not the default
                        if ($currentImgPath !== '/assets/images/services/default.png' && strpos($currentImgPath, '/assets/images/services/') === 0) {
                            $oldImagePath = __DIR__ . '/../../public' . $currentImgPath;
                            if (file_exists($oldImagePath)) {
                                unlink($oldImagePath);
                            }
                        }
                    } else {
                        error_log("Failed to move uploaded file: " . $_FILES['service_img']['tmp_name'] . " to " . $uploadPath);
                    }
                }
                
                // Update service with image path
                $stmt = $this->getPDO()->prepare("
                    UPDATE service 
                    SET service_name = ?, service_desc = ?, service_fee = ?, service_img = ?
                    WHERE service_code = ?
                ");
                $result = $stmt->execute([$serviceName, $serviceDesc, $serviceFee, $serviceImgPath, $serviceCode]);
                
                if ($result) {
                    header('Location: /admin/services/add?message=Service updated successfully');
                } else {
                    header('Location: /admin/services/add?error=Failed to update service');
                }
                exit;
            } catch (\PDOException $e) {
                error_log("Error updating service: " . $e->getMessage());
                header('Location: /admin/services/add?error=' . urlencode('Database error: ' . $e->getMessage()));
                exit;
            } catch (\Exception $e) {
                error_log("General error updating service: " . $e->getMessage());
                header('Location: /admin/services/add?error=' . urlencode('Error: ' . $e->getMessage()));
                exit;
            }
        }
        
        // If not a POST request, redirect back to the form
        header('Location: /admin/services/add');
        exit;
    }

    /**
     * Archive a service (mark as inactive) instead of deleting it
     */
    public function archiveService() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
            try {
                $serviceId = filter_var($_POST['service_id'], FILTER_SANITIZE_NUMBER_INT);
                
                // Check if service exists
                $pdo = $this->getPDO();
                $checkStmt = $pdo->prepare("SELECT service_code FROM service WHERE service_code = ?");
                $checkStmt->execute([$serviceId]);
                
                if (!$checkStmt->fetch()) {
                    header("Location: /admin/services/add?error=service_not_found");
                    exit;
                }
                
                // Archive the service by setting status to inactive
                $stmt = $pdo->prepare("UPDATE service SET status = 'ARCHIVED', updated_at = NOW() WHERE service_code = ?");
                $success = $stmt->execute([$serviceId]);
                
                if ($success) {
                    header("Location: /admin/services/add?message=Service successfully archived");
                } else {
                    header("Location: /admin/services/add?error=archive_failed");
                }
            } catch (\Exception $e) {
                error_log("Error archiving service: " . $e->getMessage());
                header("Location: /admin/services/add?error=archive_failed");
            }
            exit;
        }
        
        header("Location: /admin/services/add");
        exit;
    }
    
    /**
     * Restore an archived service
     */
    public function restoreService() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['service_id'])) {
            try {
                $serviceId = filter_var($_POST['service_id'], FILTER_SANITIZE_NUMBER_INT);
                $pdo = $this->getPDO();
                
                // Update service status to active
                $stmt = $pdo->prepare("UPDATE service SET status = 'ACTIVE', updated_at = NOW() WHERE service_code = ? AND status = 'ARCHIVED'");
                $success = $stmt->execute([$serviceId]);
                
                if ($success) {
                    header("Location: /admin/archived?service_restored=1");
                } else {
                    header("Location: /admin/archived?error=restore_failed");
                }
            } catch (\Exception $e) {
                error_log("Error restoring service: " . $e->getMessage());
                header("Location: /admin/archived?error=restore_failed");
            }
            exit;
        }
        
        header("Location: /admin/archived");
        exit;
    }

    /**
     * Get archived services
     * 
     * @return array List of archived services
     */
    private function getArchivedServices() {
        try {
            $pdo = $this->getPDO();
            $stmt = $pdo->prepare("
                SELECT s.*
                FROM service s
                WHERE s.status = 'ARCHIVED'
                ORDER BY s.service_name ASC
            ");
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_ASSOC);
        } catch (\Exception $e) {
            error_log("Error fetching archived services: " . $e->getMessage());
            return [];
        }
    }
}
