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
        
        // Build query based on filters
        if ($status === 'pending') {
            $appointments = $model->getAppointmentsByStatus('pending');
            $filterTitle = 'Pending Appointments';
        } else if ($period === 'next-week') {
            $startDate = date('Y-m-d');
            $endDate = date('Y-m-d', strtotime('+7 days'));
            $appointments = $model->getAppointmentsByDateRange($startDate, $endDate);
            $filterTitle = 'Upcoming Appointments (Next 7 Days)';
        } else {
            $appointments = $model->getAppointmentsByDate($date);
            $filterTitle = 'Appointments for ' . date('F j, Y', strtotime($date));
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
            WHERE a.status = 'pending' AND DATE(a.preferred_date) >= ?
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
            WHERE a.preferred_date BETWEEN ? AND ? AND a.status = 'confirmed'
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
}
