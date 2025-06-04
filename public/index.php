<?php

require_once __DIR__ . '/../vendor/autoload.php'; 

use AltoRouter as Router;
use Dotenv\Dotenv;
use Config\Database;

date_default_timezone_set('Asia/Manila');


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$match = $router->match();

// Log route matching information
error_log("Request URI: " . $_SERVER['REQUEST_URI']);
error_log("Match result: " . ($match ? "Found match: " . $match['target'] : "No match found"));

if ($match) {
    list($controller, $method) = explode('#', $match['target']);
    
    error_log("Controller: $controller, Method: $method");
    
    // Special handling for controllers that need dependencies
    if ($controller === 'App\Controllers\AdminAppointmentController') {
        $db = Database::getInstance()->getConnection();
        $model = new App\Models\AdminAppointmentModel($db);
        $controllerInstance = new $controller($model);
        error_log("Created AdminAppointmentController with dependencies");
    } else {
        $controllerInstance = new $controller();
    }
    
    call_user_func_array([$controllerInstance, $method], $match['params']);
} else {
    // Optional: render a 404 view
    error_log("404 Not Found - " . $_SERVER['REQUEST_URI']);
    http_response_code(404);
    echo "404 Not Found";
}

if (isset($_GET['page']) && $_GET['page'] == 'appointment') {
    $controller = new HomeController();
    $controller->appointment();
}

