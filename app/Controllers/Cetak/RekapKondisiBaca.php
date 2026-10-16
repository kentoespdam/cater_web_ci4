<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\FlattenByKondisi;
use App\Libraries\KondisiToExcel;
use App\Services\BacaMeterQueryService;
use CodeIgniter\HTTP\ResponseInterface;

class RekapKondisiBaca extends BaseController
{
    public function __construct(
        private readonly ?BacaMeterQueryService $queryService = null,
    ) {
    }

    private function getQueryService(): BacaMeterQueryService
    {
        return $this->queryService ?? new BacaMeterQueryService();
    }

    public function index(): ResponseInterface
    {
        $req = (object) request()->getGet();

        if (!isset($req->tahun) || !isset($req->bulan) || empty($req->tahun) || empty($req->bulan)) {
            return response()->setJSON([
                "rows" => [],
                "total" => 0,
                "footer" => null,
            ]);
        }

        $bulan = $req->bulan < 10 ? '0' . $req->bulan : $req->bulan;
        $periode = "{$req->tahun}-{$bulan}";

        [$tglAwal, $tglAkhir] = get_start_end_date_from_periode($periode);

        $data = $this->getQueryService()->getKondisiBaca($tglAwal, $tglAkhir);

        $flattenKondisi = new FlattenByKondisi($data, $periode);
        $result = $flattenKondisi->get();

        $filename = "rekap_kondisi_baca-" . date('Y-m-d-His');

        $toExcel = new KondisiToExcel($result, $filename);

        return $toExcel->download();
    }
}
