<?php

namespace App\Controllers;

class AdminController extends BaseController{
    // para rani makakita sa sidebar og navbar
    public function index() {
        $this->render('admin/index');
    }
    public function appointment() {
        $this->render('admin/appointment');
    }
    public function doctor(){
        $this->render('admin/doctor');
    }
}