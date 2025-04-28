<?php

namespace App\Controllers;

class HomeController extends BaseController {
    public function homepage() {
        $this->render('home/homepage');
    }
    // about page
    public function aboutpage() {
        $this->render('home/aboutpage');
    }
    
}
