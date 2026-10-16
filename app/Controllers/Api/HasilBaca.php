<?php

declare(strict_types=1);

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Models\BacaMeterModel;
use App\Models\Sikompak\CustModel;
use App\Services\HasilBacaSummaryService;
use CodeIgniter\HTTP\ResponseInterface;

final class HasilBaca extends BaseController
{
    public function __construct(
        private readonly ?HasilBacaSummaryService $summaryService = null,
    ) {
    }

    public function index(): ResponseInterface
    {
        $requestedDate = request()->getGet("periode");

        if (!$requestedDate) {
            $requestedDate = date('Y-m');
        }

        $service = $this->summaryService ?? new HasilBacaSummaryService();
        $data = $service->getSummaryData((string) $requestedDate);

        return $this->response->setJSON($data);
    }

    public function getDataGagal(): ResponseInterface
    {
        $req = (object) request()->getGet();

        if (!isset($req->petugas) || !isset($req->periode) || empty($req->petugas) || empty($req->periode)) {
            return $this->response->setJSON([]);
        }
        $skrng = new \DateTime("$req->periode-01");
        $tglAwal = date("Y-m-d", $skrng->getTimestamp());
        $akhir = new \DateTime($skrng->format('Y-m-t'));
        $tglAkhir = date('Y-m-d', $akhir->getTimestamp());

        $hasilBacaModel = new BacaMeterModel();
        $custModel = new CustModel();

        $hasilBacaData = $hasilBacaModel->getDataGagalPerUser($tglAwal, $tglAkhir, $req->petugas);
        $noPelanggan = array_map(function ($item) {
            return $item->nosamw;
        }, $hasilBacaData);
        $custData = $custModel->getDetailKampungPelanggan($noPelanggan);

        return $this->response->setJSON([
            "id" => $req->petugas,
            "total" => count($custData),
            "rows" => $custData,
        ]);
    }
}
