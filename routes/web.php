<?php

$router->setBasePath(''); // Set this if your app is in a subdirectory

// Define routes
// Home routes
$router->map('GET', '/', 'App\Controllers\HomeController#homepage', 'home');

$router->map('GET', '/about', 'App\Controllers\HomeController#aboutpage', 'about');
$router->map('GET', '/reviews', 'App\Controllers\HomeController#reviews', 'reviews');
$router->map('GET', '/services', 'App\Controllers\HomeController#services', 'services');
$router->map('GET', '/products', 'App\Controllers\HomeController#products', 'products');
$router->map('GET', '/appointment', 'App\Controllers\HomeController#appointment', 'appointmentpage');
$router->map('GET', '/vaccination', 'App\Controllers\HomeController#vaccination', 'vaccination');
$router->map('GET', '/deworming', 'App\Controllers\HomeController#deworming', 'deworming');
$router->map('GET', '/antiparasitic', 'App\Controllers\HomeController#antiparasitic', 'antiparasitic');
$router->map('GET', '/surgeries', 'App\Controllers\HomeController#surgeries', 'surgeries');
$router->map('GET', '/grooming', 'App\Controllers\HomeController#grooming', 'grooming');
$router->map('GET', '/treatment', 'App\Controllers\HomeController#treatment', 'treatment');
$router->map('GET', '/confinement', 'App\Controllers\HomeController#confinement', 'confinement');



// Admin routes
$router->map('GET', '/index', 'App\Controllers\AdminController#index', 'index');
// Auth routes
$router->map('GET', '/login', 'App\Controllers\AuthController#renderLogin', 'render-login');
$router->map('GET', '/register', 'App\Controllers\AuthController#renderRegister', 'render-register');
$router->map('POST', '/register', 'App\Controllers\AuthController#register', 'register');
