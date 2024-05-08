<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;
use App\Models\KondisiModel;
use App\Models\Sikompak\CabangModel;
use CodeIgniter\HTTP\ResponseInterface;

class HasilBaca extends BaseController
{
    public function index()
    {
        helper('bulan');
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");
        $cabangModel = new CabangModel();
        $kondisiModel = new KondisiModel();
        $cabangData = $cabangModel->orderBy("id_cabang", "asc")->findAll();
        return $view->setVar("session", $session)
            ->setVar("cabangs", $cabangData)
            ->setVar("kondisis", $kondisiModel->findAll())
            ->render("laporan/hasil_baca");
    }
}
