<?php

namespace App\Controllers\Master;

use App\Controllers\BaseController;
use App\Models\Sikompak\CabangModel;

class User extends BaseController
{
    public function index()
    {
        helper('bulan');
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        if ($session['kdStatus'] !== "admin")
            return redirect()->to('/');

        $cabangModel = new CabangModel();
        $cabangList = $cabangModel->findAll();

        return $view->setVar("session", $session)
            ->setVar("cabangList", $cabangList)
            ->render('master/user');
    }

    public function add()
    {
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        if ($session['kdStatus'] !== "admin")
            return redirect()->to('/');
        return $view->setVar("session", $session)
            ->render('master/user_add');
    }
}
