<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\Sikompak\CabangModel;

class HasilBaca0 extends BaseController
{
    public function index()
    {
        helper("bulan");
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        $cabangModel = new CabangModel();
        return $view->setVar("session", $session)
            ->setVar("cabangList", $cabangModel->findAll())
            ->render("laporan/hasilBaca0");
    }
}
