<?php

namespace Config;

use PDO;
use PDOException;

class Database
{
    private static $instance = null;
    private $connection;

    private function __construct()
    {
        $host = $_ENV['DB_HOST'];
        $port = $_ENV['DB_PORT'];
        $dbname = $_ENV['DB_NAME'];
        $user = $_ENV['DB_USER'];
        $password = $_ENV['DB_PASSWORD'];

        // Log connection attempt
        error_log("Attempting database connection to: $host:$port, database: $dbname");
        error_log("Database credentials - User: $user, Password present: " . (!empty($password) ? "Yes" : "No"));
        
        $dsn = "pgsql:host=$host;port=$port;dbname=$dbname";

        try {
            $this->connection = new PDO($dsn, $user, $password, [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ]); 
            error_log("Database connection successful");
            
            // Check and update schema if needed
            $this->checkAndUpdateSchema();
        } catch (PDOException $e) {
            error_log("Database connection failed: " . $e->getMessage());
            die("Connection failed: ") . $e->getMessage();
        }
    }
    
    /**
     * Check if product table has required columns and add them if missing
     */
    private function checkAndUpdateSchema() {
        try {
            // Check if prod_status column exists in product table
            $stmt = $this->connection->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'product' AND column_name = 'prod_status'
            ");
            
            if ($stmt->rowCount() === 0) {
                // Add prod_status column to product table
                $this->connection->exec("
                    ALTER TABLE product 
                    ADD COLUMN prod_status VARCHAR(20) DEFAULT 'ACTIVE'
                ");
                error_log("Added prod_status column to product table");
            }
            
            // Check if updated_at column exists in product table
            $stmt = $this->connection->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'product' AND column_name = 'updated_at'
            ");
            
            if ($stmt->rowCount() === 0) {
                // Add updated_at column to product table
                $this->connection->exec("
                    ALTER TABLE product 
                    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ");
                error_log("Added updated_at column to product table");
            }
            
            // Check if status column exists in veterinary_staff table
            $stmt = $this->connection->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'veterinary_staff' AND column_name = 'status'
            ");
            
            if ($stmt->rowCount() === 0) {
                // Add status column to veterinary_staff table
                $this->connection->exec("
                    ALTER TABLE veterinary_staff 
                    ADD COLUMN status VARCHAR(20) DEFAULT 'ACTIVE'
                ");
                error_log("Added status column to veterinary_staff table");
            }
            
            // Check if updated_at column exists in veterinary_staff table
            $stmt = $this->connection->query("
                SELECT column_name 
                FROM information_schema.columns 
                WHERE table_name = 'veterinary_staff' AND column_name = 'updated_at'
            ");
            
            if ($stmt->rowCount() === 0) {
                // Add updated_at column to veterinary_staff table
                $this->connection->exec("
                    ALTER TABLE veterinary_staff 
                    ADD COLUMN updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
                ");
                error_log("Added updated_at column to veterinary_staff table");
            }
        } catch (PDOException $e) {
            error_log("Error checking/updating schema: " . $e->getMessage());
        }
    }

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    public function getConnection()
    {
        return $this->connection;
    }
}
