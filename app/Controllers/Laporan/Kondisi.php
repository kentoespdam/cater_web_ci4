<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\KondisiModel;

class Kondisi extends BaseController
{
    public function index()
    {
        helper("bulan");
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        $kondisiModel = new KondisiModel();
        return $view->setVar("session", $session)
            ->setVar("kondisiList", $kondisiModel->findAll())
            ->render("laporan/kondisi");
    }
}
