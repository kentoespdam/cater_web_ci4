<?php

declare(strict_types=1);

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Services\Verif0MeterReconciliationService;
use CodeIgniter\HTTP\ResponseInterface;

class GrafikVerifikasi0 extends BaseController
{
    public function __construct(
        private readonly ?Verif0MeterReconciliationService $service = null,
    ) {
    }

    public function index(): ResponseInterface
    {
        $tahun = filter_var($this->request->getGet('tahun'), FILTER_VALIDATE_INT);

        if ($tahun === false || $tahun < 2000 || $tahun > 2100) {
            return $this->response->setStatusCode(400)->setJSON([
                'status' => 'error',
                'message' => 'Tahun tidak valid.',
            ]);
        }

        $service = $this->service ?? new Verif0MeterReconciliationService();
        $snapshotPath = $service->getSnapshotPath((int) $tahun);

        if (! is_file($snapshotPath)) {
            return $this->response->setStatusCode(404)->setJSON([
                'status' => 'not_found',
                'message' => "Snapshot verifikasi 0 meter untuk tahun {$tahun} belum tersedia.",
                'tahun' => (int) $tahun,
                'cabang' => [],
            ]);
        }

        $jsonContent = file_get_contents($snapshotPath);

        if ($jsonContent === false) {
            return $this->response->setStatusCode(500)->setJSON([
                'status' => 'error',
                'message' => 'Gagal membaca file snapshot.',
            ]);
        }

        return $this->response
            ->setStatusCode(200)
            ->setContentType('application/json')
            ->setBody($jsonContent);
    }
}
