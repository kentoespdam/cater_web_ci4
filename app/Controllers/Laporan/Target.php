<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;

class Target extends BaseController
{
    public function index()
    {
        helper("bulan");
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        return $view->setVar("session", $session)
            ->render("laporan/target");
    }
}
