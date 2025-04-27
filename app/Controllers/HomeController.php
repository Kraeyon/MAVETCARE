<?php

namespace App\Controllers;

class HomeController extends BaseController {
    public function homepage() {
        $this->render('home/homepage');
    }
    public function aboutpage() {
        $this->render('home/aboutpage');
    }
}