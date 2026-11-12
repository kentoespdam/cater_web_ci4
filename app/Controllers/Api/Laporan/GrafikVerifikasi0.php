<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Verif0MeterGrafikService;

class GrafikVerifikasi0 extends BaseController
{
    public function index(): \CodeIgniter\HTTP\ResponseInterface
    {
        $tahun = filter_var($this->request->getGet('tahun'), FILTER_VALIDATE_INT);
        if ($tahun === false || $tahun < 2000 || $tahun > 2100) {
            return $this->response->setStatusCode(400)->setJSON(['message' => 'Tahun tidak valid.']);
        }

        return $this->response->setJSON((new Verif0MeterGrafikService())->getBulanan($tahun));
    }
}
