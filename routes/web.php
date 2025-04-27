<?php

$router->setBasePath(''); // Set this if your app is in a subdirectory

// Define routes
// Home routes
$router->map('GET', '/', 'App\Controllers\HomeController#homepage', 'home');
$router->map('GET', '/about', 'App\Controllers\HomeController#aboutpage', 'about');

// Auth routes
$router->map('GET', '/login', 'App\Controllers\AuthController#renderLogin', 'render-login');
$router->map('GET', '/register', 'App\Controllers\AuthController#renderRegister', 'render-register');