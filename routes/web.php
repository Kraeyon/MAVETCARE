<?php

$router->setBasePath(''); // Set this if your app is in a subdirectory

// Define routes
// Home routes
$router->map('GET', '/', 'App\Controllers\HomeController#index', 'home');

// Auth routes
$router->map('GET', '/login', 'App\Controllers\AuthController#renderLogin', 'render-login');
$router->map('GET', '/register', 'App\Controllers\AuthController#renderRegister', 'render-register');