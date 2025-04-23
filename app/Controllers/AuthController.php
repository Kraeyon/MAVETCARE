<?php

namespace App\Controllers;

class AuthController extends BaseController {
    public function renderLogin() {
        $this->render('auth/login');
    }
}