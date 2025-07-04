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

// Appointment and pet-related routes (using AppointmentController)
$router->map('GET', '/appointment', 'App\Controllers\AppointmentController#index', 'appointment');
$router->map('POST', '/appointment', 'App\Controllers\AppointmentController#submitAppointment', 'submit-appointment');
$router->map('GET', '/my-appointments', 'App\Controllers\AppointmentController#myAppointments', 'my-appointments');
$router->map('GET', '/api/appointment/availability', 'App\Controllers\AppointmentController#checkSlotAvailability', 'check-slot-availability');

// Pet management
$router->map('GET', '/add-pet', 'App\Controllers\AppointmentController#showAddPetForm', 'show-add-pet');
$router->map('POST', '/add-pet', 'App\Controllers\AppointmentController#addPet', 'add-pet');

// Service-specific pages

// Admin routes
$router->map('GET', '/index', 'App\Controllers\AdminController#index', 'index');
$router->map('GET', '/admin/appointment', 'App\Controllers\AdminController#appointment', 'admin-appointment');
$router->map('GET', '/admin/doctor', 'App\Controllers\AdminController#doctor', 'doctor');
$router->map('POST', '/admin/doctor/edit/[i:staffCode]', 'App\Controllers\AdminController#editDoctorSchedule', 'edit-doctor-schedule');
$router->map('GET', '/admin/patients', 'App\Controllers\AdminController#patients', 'patients');

// Admin notification routes
$router->map('GET', '/admin/transactions', 'App\Controllers\AdminController#transactions', 'admin-transactions');
$router->map('GET', '/admin/appointments', 'App\Controllers\AdminController#viewAppointmentsFiltered', 'admin-appointments-filtered');
$router->map('GET', '/admin/inventory', 'App\Controllers\AdminController#inventoryFiltered', 'admin-inventory-filtered');
$router->map('GET', '/admin/reviews', 'App\Controllers\AdminController#reviews', 'admin-reviews');
$router->map('GET', '/admin/notifications', 'App\Controllers\AdminController#allNotifications', 'admin-all-notifications');
$router->map('GET', '/admin/archived', 'App\Controllers\AdminController#archivedItems', 'admin-archived-items');

// Add route for adding a new patient
$router->map('POST', '/admin/patients/add', 'App\Controllers\PatientController#addPatient', 'add-patient');

// Add route for updating an existing patient
$router->map('POST', '/admin/patients/update', 'App\Controllers\PatientController#updatePatient', 'update-patient');

// Add route for deleting a patient
$router->map('GET', '/admin/patients/delete/[i:id]', 'App\Controllers\PatientController#deletePatient', 'delete-patient');

// Admin dashboard routes
$router->map('POST', '/admin/appointments/update-status', 'App\Controllers\AdminAppointmentController#updateAppointmentStatus', 'update-appointment-status');
$router->map('GET', '/admin/appointments/add', 'App\Controllers\AdminAppointmentController#showAddForm', 'show-add-appointment');
$router->map('GET', '/admin/appointments/edit/[i:id]', 'App\Controllers\AdminAppointmentController#showEditForm', 'show-edit-appointment');
$router->map('POST', '/admin/appointments/update', 'App\Controllers\AdminAppointmentController#handleAppointmentUpdate', 'update-appointment');
$router->map('POST', '/admin/appointments/delete', 'App\Controllers\AdminAppointmentController#handleAppointmentDelete', 'delete-appointment');
$router->map('POST', '/admin/appointments/restore', 'App\Controllers\AdminAppointmentController#restoreAppointment', 'restore-appointment');
$router->map('POST', '/admin/appointments/get-pets', 'App\Controllers\AdminAppointmentController#getPetsByClient', 'get-pets-by-client');
$router->map('GET', '/admin/pets/add', 'App\Controllers\AdminController#showAddPetForm', 'show-add-pet-admin');
$router->map('GET', '/admin/transactions/add', 'App\Controllers\AdminController#showAddTransactionForm', 'show-add-transaction');
$router->map('GET', '/admin/services/add', 'App\Controllers\AdminController#showAddServiceForm', 'show-add-service');
$router->map('POST', '/admin/services/add', 'App\Controllers\AdminController#addService', 'add-service');
$router->map('POST', '/admin/services/update', 'App\Controllers\AdminController#updateService', 'update-service');
$router->map('POST', '/admin/services/archive', 'App\Controllers\AdminController#archiveService', 'archive-service');
$router->map('POST', '/admin/services/restore', 'App\Controllers\AdminController#restoreService', 'restore-service');

$router->map('GET', '/admin/employees', 'App\Controllers\AdminEmployeeController#index', 'employees');

// Auth routes
$router->map('GET', '/login', 'App\Controllers\AuthController#renderLogin', 'render-login');
$router->map('POST', '/login', 'App\Controllers\AuthController#login', 'login');
$router->map('POST', '/logout', 'App\Controllers\AuthController#logout', 'logout');
$router->map('GET', '/register', 'App\Controllers\AuthController#renderRegister', 'render-register');
$router->map('POST', '/register', 'App\Controllers\AuthController#register', 'register');

$router->map('POST', '/admin/employees/add', 'App\Controllers\AdminEmployeeController#addEmployee', 'add-employee');
$router->map('POST', '/admin/employees/edit', 'App\Controllers\AdminEmployeeController#editEmployee', 'edit-employee');
$router->map('POST', '/admin/employees/archive', 'App\Controllers\AdminEmployeeController#archiveEmployee', 'archive-employee');
$router->map('POST', '/admin/employees/delete', 'App\Controllers\AdminEmployeeController#archiveEmployee', 'delete-employee');
$router->map('POST', '/admin/employees/restore', 'App\Controllers\AdminController#restoreStaff', 'restore-employee');

$router->map('POST', '/admin/inventory/add', 'App\Controllers\AdminController#addProduct', 'admin-inventory-add');
$router->map('POST', '/admin/inventory/update', 'App\Controllers\AdminController#updateProduct', 'admin-inventory-update');
$router->map('POST', '/admin/inventory/archive', 'App\Controllers\AdminController#archiveProduct', 'admin-inventory-archive');
$router->map('POST', '/admin/inventory/delete', 'App\Controllers\AdminController#archiveProduct', 'admin-inventory-delete');
$router->map('POST', '/admin/inventory/restore', 'App\Controllers\AdminController#restoreProduct', 'admin-inventory-restore');

// Sales routes
$router->map('GET', '/admin/sales', 'App\Controllers\SalesController#index', 'admin-sales');
$router->map('POST', '/admin/sales/add', 'App\Controllers\SalesController#addSale', 'admin-sales-add');
$router->map('GET', '/admin/sales/details/[i:saleId]', 'App\Controllers\SalesController#getSaleDetails', 'admin-sales-details');
$router->map('GET', '/admin/sales/print/[i:saleId]', 'App\Controllers\SalesController#printReceipt', 'admin-sales-print');
$router->map('GET', '/admin/sales/filter', 'App\Controllers\SalesController#filterSales', 'admin-sales-filter');

// Supplier management routes have been removed
