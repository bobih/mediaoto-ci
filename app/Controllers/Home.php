<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        return view('welcome_message');
    }

    public function getPolicy() {
       
        return view('policy');
    }

    public function getPrivacy() {
       
        return view('privacy');
    }
}
