<?php

namespace App\Controllers;

class Home extends BaseController
{
    public function index(): string
    {
        helper('bulan');
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        return $view->setVar("session", $session)
            ->render('welcome_message');
    }
}
