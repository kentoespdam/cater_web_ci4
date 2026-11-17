<?php

declare(strict_types=1);

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Services\Verif0MeterPelangganService;
use CodeIgniter\HTTP\ResponseInterface;

final class GrafikVerifikasi0Pelanggan extends BaseController
{
    public function index(): ResponseInterface
    {
        $tahun = filter_var($this->request->getGet('tahun'), FILTER_VALIDATE_INT);
        $bulan = filter_var($this->request->getGet('bulan'), FILTER_VALIDATE_INT);
        $page = filter_var($this->request->getGet('page') ?? 1, FILTER_VALIDATE_INT);
        $rows = filter_var($this->request->getGet('rows') ?? 25, FILTER_VALIDATE_INT);
        $jenis = (string) ($this->request->getGet('jenis') ?? 'target');

        if ($tahun === false || $tahun < 2000 || $tahun > 2100 || $bulan === false || $bulan < 1 || $bulan > 12 || $page === false || $page < 1 || $rows === false || $rows < 1 || $rows > 200 || ! in_array($jenis, ['target', 'verifikasi'], true)) {
            return $this->response->setStatusCode(400)->setJSON(['status' => 'error', 'message' => 'Parameter laporan tidak valid.']);
        }
        $result = (new Verif0MeterPelangganService())->list((int) $tahun, (int) $bulan, $jenis, $this->request->getGet('satker'), $this->request->getGet('status_manual'), $this->request->getGet('status_id'), (int) $page, (int) $rows);

        return $this->response->setJSON($result);
    }
}
