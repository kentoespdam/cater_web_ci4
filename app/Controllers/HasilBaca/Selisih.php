<?php

namespace App\Controllers\Hasilbaca;

use App\Controllers\BaseController;
use App\Models\Sikompak\CabangModel;
use App\Models\Sikompak\PegawaiModel;

class Selisih extends BaseController
{
    public function index()
    {
        helper('bulan');
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");

        return $view->setVar("session", $session)
            ->setVar("cabangList", $this->getCabang())
            ->setVar("pegawaiList", $this->getPegawai())
            ->render("hasilbaca/selisih");
    }

    private function getCabang(): array
    {
        $cabangModel = new CabangModel();
        return $cabangModel->orderBy("id_cabang", "asc")->findAll();
    }

    private function getPegawai(): array
    {
        $pegawaiModel = new PegawaiModel();
        return $pegawaiModel->select("pembaca_meter AS petugas")
            ->where("pembaca_meter IS NOT NULL")
            ->where("pembaca_meter <> '-'")
            ->groupBy("pembaca_meter")
            ->findAll();
    }
}
