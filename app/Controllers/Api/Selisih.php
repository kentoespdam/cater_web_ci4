<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Services\SelisihFotoService;

class Selisih extends BaseController
{
    protected SelisihFotoService $service;

    public function __construct()
    {
        $this->service = new SelisihFotoService();
    }

    public function defaultPropertyGrid()
    {
        return $this->response->setJSON([
            "total" => 3,
            "rows" => [
                ["name" => "jmlData", "value" => 0],
                ["name" => "jmlFoto", "value" => 0],
                ["name" => "selisih", "value" => 0],
            ],
        ]);
    }

    public function index()
    {
        $request = $this->request;
        $tahun = $request->getGet('tahun') ?: date('Y');
        $bulan = $request->getGet('bulan') ?: date('m');
        $cabang = $request->getGet('cabang');
        $petugas = $request->getGet('petugas');
        $findByCabang = $request->getGet('findByCabang');

        if (!empty($cabang) && $findByCabang !== 'false') {
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

        $rows = $result['data'] ?? [];

        return $this->response->setJSON([
            'rows'  => $rows,
            'total' => count($rows),
            'propertygrid' => [
                'total' => 3,
                'rows'  => [
                    ['name' => 'jmlData', 'value' => $result['jmlData'] ?? 0],
                    ['name' => 'jmlFoto', 'value' => $result['jmlFoto'] ?? 0],
                    ['name' => 'selisih', 'value' => $result['selisih'] ?? 0],
                ],
            ],
        ]);
    }
}
