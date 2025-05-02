<?php

namespace App\Controllers;

use App\Models\UserModel;

class AuthController extends BaseController {
    public function renderLogin() {
        $this->render('auth/login');
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

}