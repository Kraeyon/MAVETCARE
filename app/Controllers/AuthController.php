<?php

require_once 'BaseController.php';

class AuthController extends BaseController
{
    public function showLogin()
    {
        $this->render('auth.login');
    }

    public function showRegister()
    {
        $this->render('auth.register');
    }

    public function showLoginRegister()
    {
        $this->render('auth.login_register');
    }
}
