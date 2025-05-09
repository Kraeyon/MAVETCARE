<?php

namespace App\Controllers;

class AdminController extends BaseController{
    // para rani makakita sa sidebar og navbar
    public function index() {
        $this->render('admin/index');
    }

    public function doctors() {
        $this->render('admin/doctors');
    }

    public function patients() {
        $this->render('admin/patients');
    }   

    public function appointment() {
        $this->render('admin/appointment');
    }
    public function doctor(){
        $this->render('admin/doctor');
    }
    public function schedule() {
        $this->render('admin/schedule');
    }

    public function inventory() {
        $this->render('admin/inventory');
    }

    public function employees() {
        $this->render('admin/employees');
    }
}
