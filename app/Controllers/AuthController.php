<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController {
    public function renderLogin() {
        $this->render('auth/login');
        
    }

    public function login()
{
    session_start(); // Ensure session is started

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Sanitize and validate inputs
        $email = filter_var(trim($_POST['email']), FILTER_VALIDATE_EMAIL);
        $password = trim($_POST['password']);

        if (!$email || !$password) {
            // Invalid input format
            $this->render('auth/login', ['error' => 'Please enter a valid email and password.']);
            return;
        }

        // Fetch the user by email
        $userModel = new UserModel();
        $user = $userModel->findByEmail($email);

        // Check if the user exists and verify the password
        if ($user && password_verify($password, $user['password'])) {
            // Store user data in session
            $_SESSION['user'] = [
                'id' => $user['id'],
                'email' => $user['email'],
                'role' => $user['role'],
                'client_code' => $user['client_code'],
                'name' => $user['client_name']
            ];

            // Redirect to the homepage or dashboard
            header('Location: /');
            exit;
        } else {
            // Handle invalid credentials
            $this->render('auth/login', ['error' => 'Invalid email or password.']);
        }
    } else {
        // Display the login form
        $this->render('auth/login');
    }
}




    public function renderRegister() {
        $this->render('auth/register');
    }
    public function register()
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = [
            'first_name' => $_POST['first_name'],
            'last_name' => $_POST['last_name'],
            'email' => $_POST['email'],
            'password' => $_POST['password'],
            'confirm_password' => $_POST['confirm_password']
        ];

        // Basic validation
        if ($data['password'] !== $data['confirm_password']) {
            die('Passwords do not match.');
        }

        $userModel = new UserModel();
        $userModel->registerClient($data);

        // Redirect to login page
        header("Location: /login");
        exit;
    }
}

// In AuthController.php, logout method
public function logout()
{
    // Destroy session and redirect to the homepage
    session_start();
    session_unset();
    session_destroy();
    
    header('Location: /login');
    exit;
}


}