<?php

$router->setBasePath(''); // Set this if your app is in a subdirectory

// Define routes
// Home routes
$router->map('GET', '/', 'App\Controllers\HomeController#homepage', 'home');

$router->map('GET', '/about', 'App\Controllers\HomeController#aboutpage', 'about');
$router->map('GET', '/reviews', 'App\Controllers\HomeController#reviews', 'reviews');
$router->map('GET', '/services', 'App\Controllers\HomeController#services', 'services');
$router->map('GET', '/products', 'App\Controllers\HomeController#products', 'products');
<<<<<<< HEAD
$router->map('GET', '/vaccination', 'App\Controllers\HomeController#vaccination', 'vaccination');
=======
$router->map('GET', '/appointment', 'App\Controllers\HomeController#appointment', 'appointmentpage');
>>>>>>> 30837c9ef6d73cbf4d5da44f6675065691bf483e

// Admin routes
$router->map('GET', '/index', 'App\Controllers\AdminController#index', 'index');
// Auth routes
$router->map('GET', '/login', 'App\Controllers\AuthController#renderLogin', 'render-login');
$router->map('GET', '/register', 'App\Controllers\AuthController#renderRegister', 'render-register');

$router->map('GET', '/appointment', 'HomeController#appointment');
