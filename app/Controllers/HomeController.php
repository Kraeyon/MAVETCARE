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
    //reviews page
    public function reviews() {
        $this->render('home/reviews');
    }
    // services page
    public function services() {
        $this->render('home/services');
    }
    // products page
    public function products() {
        $this->render('home/products');
    }
    // appointment page
    public function appointment() {
        $this->render('home/appointmentpage');
    }
    // vaccination page
    public function vaccination() {
        $this->render('home/vaccination');
    }
    // deworming page
    public function deworming() {
        $this->render('home/deworming');
    }
    // anti-parasitic page
    public function antiparasitic() {
        $this->render('home/antiparasitic');
    }
    // surgeries page
    public function surgeries() {
        $this->render('home/surgeries');
    }
    // grooming page
    public function grooming() {
        $this->render('home/grooming');
    }
    // treatment page
    public function treatment() {
        $this->render('home/treatment');
    }
    // confinement page
    public function confinement() {
        $this->render('home/confinement');
    }
}
