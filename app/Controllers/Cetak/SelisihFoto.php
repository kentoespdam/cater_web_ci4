<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Services\SelisihFotoService;

class SelisihFoto extends BaseController
{
    protected SelisihFotoService $service;

    public function __construct()
    {
        $this->service = new SelisihFotoService();
    }

    public function index()
    {
        helper("bulan");

        return view("cetak/selisih", ["data" => $this->getData()]);
    }

    private function getData()
    {
        $request = $this->request;
        $tahun = $request->getGet('tahun') ?: date('Y');
        $bulan = $request->getGet('bulan') ?: date('m');
        $cabang = $request->getGet('cabang');
        $petugas = $request->getGet('petugas');
        $findByCabang = $request->getGet('findByCabang');

        if (!empty($cabang) && $findByCabang === 'on') {
            $result = $this->service->findByCabang($cabang, $bulan, $tahun);
        } elseif (!empty($petugas)) {
            $result = $this->service->findByPegawai($petugas, $bulan, $tahun);
        } else {
            $result = [
                'jmlData' => 0,
                'jmlFoto' => 0,
                'selisih' => 0,
                'data'    => [],
            ];
        }

        return (object) [
            "rows" => $result['data'],
            "total" => count($result['data']),
            "propertygrid" => (object) [
                "total" => 3,
                "rows" => [
                    (object) ["name" => "jmlData", "value" => $result['jmlData']],
                    (object) ["name" => "jmlFoto", "value" => $result['jmlFoto']],
                    (object) ["name" => "selisih", "value" => $result['selisih']],
                ],
            ],
        ];
    }
}
