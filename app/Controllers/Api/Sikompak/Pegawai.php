<?php

namespace App\Controllers\Api\Sikompak;

use App\Controllers\BaseController;
use App\Models\Sikompak\PegawaiModel;
use CodeIgniter\HTTP\ResponseInterface;

class Pegawai extends BaseController
{
    public function index()
    {
        $cabang = request()->getGet("cabang");
        if (!isset($cabang)) return response()->setJSON([]);
        $pegawaiModel = new PegawaiModel();
        $pegawais = $pegawaiModel->select("nik, pembaca_meter as user, wil, cabang")
            ->where("pembaca_meter IS NOT NULL")
            ->where("wil", $cabang)
            ->groupBy("pembaca_meter")
            ->findAll();
        return response()->setJSON($pegawais);
    }

    public function findKampung()
    {
        $data = (object)request()->getGet();
        if (!isset($data->cabang) || empty($data->cabang)) return response()->setJSON([]);

        $pegawaiModel = new PegawaiModel();
        $builder = $pegawaiModel->select("nik, nama, pembaca_meter as user, wil, cabang")
            ->where("pembaca_meter IS NOT NULL")
            ->where("wil", $data->cabang);
        if (isset($data->petugas) || !empty($data->petugas)) $builder->where("pembaca_meter", $data->petugas);
        $pegawais = $builder->findAll();

        return response()->setJSON($pegawais);
    }
}
