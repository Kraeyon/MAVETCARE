<?php

namespace App\Controllers;

use Config\Database;

class BaseController {

    protected function getViewPath(string $relativePath): string {
        $path = __DIR__ . "/../Views/{$relativePath}.php";

        if (!file_exists($path)) {
           echo "View not found: {$relativePath}";
        }

        return $path;
    }
    
    public function render($view, $data = []) {
        // Start output buffering only once
        ob_start();

        $viewPath = $this->getViewPath($view);

        extract($data);

        include $viewPath;

        $content = ob_get_clean();

        echo $content;
    }
    
    /**
     * Get the current user from session
     * 
     * @return array|null User data or null if not logged in
     */
    protected function getUser() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        return $_SESSION['user'] ?? null;
    }
    
    /**
     * Get database connection
     * 
     * @return \PDO Database connection
     */
    protected function getPDO() {
        return Database::getInstance()->getConnection();
    }
}