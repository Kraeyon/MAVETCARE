<?php

require_once __DIR__ . '/../vendor/autoload.php'; 

use AltoRouter as Router;
use Dotenv\Dotenv;

date_default_timezone_set('Asia/Manila');


$dotenv = Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


$router = new Router();

require_once __DIR__ . '/../routes/web.php';

$match = $router->match();

if ($match) {
    list($controller, $method) = explode('#', $match['target']);
    $controllerInstance = new $controller();
    call_user_func_array([$controllerInstance, $method], $match['params']);
} else {
    // Optional: render a 404 view
    http_response_code(404);
    echo "404 Not Found";
}

if ($_GET['page'] == 'appointment') {
    $controller = new HomeController();
    $controller->appointment();
}

