<?php

namespace App\Controllers;

use Config\Database;

class SalesController extends BaseController {
    
    /**
     * Display the sales page
     */
    public function index() {
        $this->render('admin/sales');
    }
    
    /**
     * Add a new sale
     */
    public function addSale() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: /admin/sales');
            exit;
        }
        
        // Get the database connection
        $db = Database::getInstance()->getConnection();
        
        // Start transaction
        $db->beginTransaction();
        
        try {
            // Check if there are products in the sale
            if (!isset($_POST['products']) || empty($_POST['products'])) {
                header('Location: /admin/sales?error=no_items');
                exit;
            }
            
            $totalAmount = isset($_POST['total_amount']) ? floatval($_POST['total_amount']) : 0;
            
            // Insert the sale record
            $stmt = $db->prepare("
                INSERT INTO sales (total_amount)
                VALUES (?)
            ");
            $stmt->execute([$totalAmount]);
            
            // Get the sale ID
            $saleId = $db->lastInsertId();
            
            // Process each product in the sale
            foreach ($_POST['products'] as $product) {
                $productCode = $product['product_code'];
                $quantity = intval($product['quantity']);
                $price = floatval($product['price']);
                
                // Check if there's enough stock
                $stockStmt = $db->prepare("SELECT prod_stock FROM product WHERE prod_code = ?");
                $stockStmt->execute([$productCode]);
                $currentStock = $stockStmt->fetchColumn();
                
                if ($currentStock < $quantity) {
                    // Not enough stock, rollback and show error
                    $db->rollBack();
                    header('Location: /admin/sales?error=insufficient_stock');
                    exit;
                }
                
                // Insert sale detail
                $detailStmt = $db->prepare("
                    INSERT INTO sales_details (sale_id, prod_code, quantity, price)
                    VALUES (?, ?, ?, ?)
                ");
                $detailStmt->execute([$saleId, $productCode, $quantity, $price]);
                
                // Update product stock
                $updateStmt = $db->prepare("
                    UPDATE product
                    SET prod_stock = prod_stock - ?
                    WHERE prod_code = ?
                ");
                $updateStmt->execute([$quantity, $productCode]);
            }
            
            // Commit the transaction
            $db->commit();
            
            // Redirect back with success message
            header('Location: /admin/sales?added=true');
            exit;
            
        } catch (\Exception $e) {
            // Something went wrong, rollback
            $db->rollBack();
            
            // Log the error
            error_log('Error adding sale: ' . $e->getMessage());
            
            // Redirect with error message
            header('Location: /admin/sales?error=' . urlencode($e->getMessage()));
            exit;
        }
    }
    
    /**
     * Get sale details for a specific sale
     */
    public function getSaleDetails($saleId) {
        // Get the database connection
        $db = Database::getInstance()->getConnection();
        
        // Get sale info
        $saleStmt = $db->prepare("
            SELECT * FROM sales WHERE sale_id = ?
        ");
        $saleStmt->execute([$saleId]);
        $sale = $saleStmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$sale) {
            // Sale not found, return 404
            http_response_code(404);
            echo json_encode(['error' => 'Sale not found']);
            exit;
        }
        
        // Get sale details
        $detailsStmt = $db->prepare("
            SELECT sd.*, p.prod_name as product_name
            FROM sales_details sd
            JOIN product p ON sd.prod_code = p.prod_code
            WHERE sd.sale_id = ?
        ");
        $detailsStmt->execute([$saleId]);
        $items = $detailsStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Return JSON response
        header('Content-Type: application/json');
        echo json_encode([
            'sale' => $sale,
            'items' => $items
        ]);
        exit;
    }
    
    /**
     * Filter sales via AJAX
     */
    public function filterSales() {
        try {
            // Get the database connection
            $db = Database::getInstance()->getConnection();
            
            // Define filter parameters
            $filter = isset($_GET['filter']) ? $_GET['filter'] : 'today';
            $dateRange = isset($_GET['date_range']) ? $_GET['date_range'] : '';
            
            // Query sales based on filter
            $params = [];
            $whereClause = '';
            
            if ($filter === 'today') {
                $whereClause = "WHERE DATE(s.sale_date) = CURRENT_DATE";
                $filterTitle = "Today's Sales";
            } elseif ($filter === 'week') {
                $whereClause = "WHERE s.sale_date >= CURRENT_DATE - INTERVAL '7 days'";
                $filterTitle = "Last 7 Days (Including Today)";
            } elseif ($filter === 'month') {
                $whereClause = "WHERE s.sale_date >= CURRENT_DATE - INTERVAL '30 days'";
                $filterTitle = "Last 30 Days (Including Today)";
            } elseif ($filter === 'custom' && !empty($dateRange)) {
                // Parse the date range (format: YYYY-MM-DD - YYYY-MM-DD)
                $dates = explode(' - ', $dateRange);
                if (count($dates) === 2) {
                    $startDate = $dates[0];
                    $endDate = $dates[1];
                    // Check if today is included in the range
                    $today = date('Y-m-d');
                    $includestoday = ($today >= $startDate && $today <= $endDate) ? true : false;
                    $whereClause = "WHERE DATE(s.sale_date) BETWEEN ? AND ?";
                    $params = [$startDate, $endDate];
                    $filterTitle = "Sales from $startDate to $endDate" . ($includestoday ? " (Including Today)" : "");
                }
            } else {
                $whereClause = ""; // All sales
                $filterTitle = "All Sales (Including Today)";
            }
            
            // PostgreSQL compatible query
            $query = "
                SELECT 
                    s.sale_id, 
                    s.sale_date, 
                    s.total_amount, 
                    COUNT(sd.sale_id) AS item_count
                FROM 
                    sales s
                LEFT JOIN 
                    sales_details sd ON s.sale_id = sd.sale_id
                $whereClause
                GROUP BY 
                    s.sale_id, s.sale_date, s.total_amount
                ORDER BY 
                    s.sale_date DESC
            ";
            
            $stmt = $db->prepare($query);
            $stmt->execute($params);
            $sales = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            
            // Calculate total sales amount for the filter
            $totalQuery = "
                SELECT COALESCE(SUM(total_amount), 0) as total
                FROM sales s
                $whereClause
            ";
            
            $totalStmt = $db->prepare($totalQuery);
            $totalStmt->execute($params);
            $totalSales = $totalStmt->fetch(\PDO::FETCH_ASSOC)['total'];
            
            // Return a simple JSON response for debugging
            header('Content-Type: application/json');
            
            // Generate sales HTML if we have sales
            $salesHTML = '';
            if (!empty($sales)) {
                foreach ($sales as $sale) {
                    $saleDate = date('M d, Y h:i A', strtotime($sale['sale_date']));
                    $saleAmount = number_format($sale['total_amount'], 2);
                    $salesHTML .= '<tr>
                        <td class="fw-medium">#' . $sale['sale_id'] . '</td>
                        <td>' . $saleDate . '</td>
                        <td><span class="badge bg-info">' . $sale['item_count'] . ' items</span></td>
                        <td class="fw-bold">₱' . $saleAmount . '</td>
                        <td>
                            <div class="d-flex justify-content-center gap-2">
                                <button class="btn btn-sm btn-outline-primary" 
                                        onclick="viewSaleDetails(' . $sale['sale_id'] . ')" 
                                        title="View Details">
                                    <i class="bi bi-eye"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-secondary" 
                                        onclick="printReceipt(' . $sale['sale_id'] . ')" 
                                        title="Print Receipt">
                                    <i class="bi bi-printer"></i>
                                </button>
                            </div>
                        </td>
                    </tr>';
                }
            }
            
            // Return the results
            echo json_encode([
                'isEmpty' => empty($sales),
                'sales' => $salesHTML,
                'totalSales' => number_format($totalSales, 2),
                'count' => count($sales),
                'filterTitle' => $filterTitle
            ]);
            
        } catch (\Exception $e) {
            // Log the error
            error_log('Error filtering sales: ' . $e->getMessage());
            
            // Return a very simple error response
            header('Content-Type: application/json');
            http_response_code(500);
            echo json_encode(['error' => 'Error: ' . $e->getMessage()]);
        }
        exit;
    }
    
    /**
     * Generate a printable receipt for a sale
     */
    public function printReceipt($saleId) {
        // Get the database connection
        $db = Database::getInstance()->getConnection();
        
        // Get sale info
        $saleStmt = $db->prepare("
            SELECT * FROM sales WHERE sale_id = ?
        ");
        $saleStmt->execute([$saleId]);
        $sale = $saleStmt->fetch(\PDO::FETCH_ASSOC);
        
        if (!$sale) {
            // Sale not found, redirect to sales page
            header('Location: /admin/sales?error=sale_not_found');
            exit;
        }
        
        // Get sale details
        $detailsStmt = $db->prepare("
            SELECT sd.*, p.prod_name as product_name
            FROM sales_details sd
            JOIN product p ON sd.prod_code = p.prod_code
            WHERE sd.sale_id = ?
        ");
        $detailsStmt->execute([$saleId]);
        $items = $detailsStmt->fetchAll(\PDO::FETCH_ASSOC);
        
        // Render the receipt template
        $this->render('admin/receipt', [
            'sale' => $sale,
            'items' => $items
        ]);
    }
    
    /**
     * Check if sales tables exist
     */
    private function checkSalesTables($db) {
        // Check if sales table exists
        $salesTableQuery = "
            SELECT 1 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() 
            AND table_name = 'sales'
        ";
        $salesTableExists = $db->query($salesTableQuery)->fetchColumn();
        
        if (!$salesTableExists) {
            throw new \Exception("Sales table does not exist. Please run the SQL to create the sales tables.");
        }
        
        // Check if sales_details table exists
        $detailsTableQuery = "
            SELECT 1 
            FROM information_schema.tables 
            WHERE table_schema = DATABASE() 
            AND table_name = 'sales_details'
        ";
        $detailsTableExists = $db->query($detailsTableQuery)->fetchColumn();
        
        if (!$detailsTableExists) {
            throw new \Exception("Sales_details table does not exist. Please run the SQL to create the sales tables.");
        }
        
        // Check sales table structure
        $salesStructureQuery = "DESCRIBE sales";
        try {
            $salesColumns = $db->query($salesStructureQuery)->fetchAll(\PDO::FETCH_COLUMN, 0);
            $requiredSalesColumns = ['sale_id', 'sale_date', 'total_amount'];
            $missingSalesColumns = array_diff($requiredSalesColumns, $salesColumns);
            
            if (!empty($missingSalesColumns)) {
                throw new \Exception("Sales table is missing required columns: " . implode(', ', $missingSalesColumns));
            }
        } catch (\PDOException $e) {
            throw new \Exception("Error checking sales table structure: " . $e->getMessage());
        }
        
        // Check sales_details table structure
        $detailsStructureQuery = "DESCRIBE sales_details";
        try {
            $detailsColumns = $db->query($detailsStructureQuery)->fetchAll(\PDO::FETCH_COLUMN, 0);
            $requiredDetailsColumns = ['sales_detail_id', 'sale_id', 'prod_code', 'quantity', 'price', 'subtotal'];
            $missingDetailsColumns = array_diff($requiredDetailsColumns, $detailsColumns);
            
            if (!empty($missingDetailsColumns)) {
                throw new \Exception("Sales_details table is missing required columns: " . implode(', ', $missingDetailsColumns));
            }
        } catch (\PDOException $e) {
            throw new \Exception("Error checking sales_details table structure: " . $e->getMessage());
        }
    }
} 