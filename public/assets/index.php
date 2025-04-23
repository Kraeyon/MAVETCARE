<?php
// Define base path
define('BASE_PATH', dirname(__FILE__));

// Load configuration
require_once BASE_PATH . '/app/config/database.php';

// Load core files
require_once BASE_PATH . '/app/core/Router.php';
require_once BASE_PATH . '/app/core/Controller.php';

// Initialize router
$router = new Router();

// Include routes file
require_once BASE_PATH . '/app/routes/web.php';

// Dispatch the request
$router->dispatch();