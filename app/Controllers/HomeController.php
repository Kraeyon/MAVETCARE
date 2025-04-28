<?php

namespace App\Controllers;

class HomeController extends BaseController {
    public function homepage() {
        $this->render('home/homepage');
    }
}
