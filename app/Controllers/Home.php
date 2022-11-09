<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index()
    {
        $JWT = new \Firebase\JWT\JWT;
        return view('welcome_message');
    }
}
