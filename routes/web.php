<?php

$router->setBasePath(''); // Set this if your app is in a subdirectory

// Define routes
// Home routes
$router->map('GET', '/', 'App\Controllers\HomeController#homepage', 'home');

$router->map('GET', '/about', 'App\Controllers\HomeController#aboutpage', 'about');
$router->map('GET', '/reviews', 'App\Controllers\HomeController#reviews', 'reviews');
// Reviews JSON API
$router->map('GET', '/api/reviews', 'App\Controllers\ReviewController#getAll', 'api_reviews_all');
$router->map('GET', '/api/reviews/average', 'App\Controllers\ReviewController#getAverage', 'api_reviews_average');
$router->map('GET', '/api/reviews/distribution', 'App\Controllers\ReviewController#getDistribution', 'api_reviews_distribution');
$router->map('POST', '/api/reviews', 'App\Controllers\ReviewController#submit', 'api_reviews_submit');


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
$router->map('POST', '/login', 'App\Controllers\AuthController#login', 'login');
$router->map('POST', '/logout', 'App\Controllers\AuthController#logout', 'logout');
$router->map('GET', '/register', 'App\Controllers\AuthController#renderRegister', 'render-register');
$router->map('POST', '/register', 'App\Controllers\AuthController#register', 'register');
