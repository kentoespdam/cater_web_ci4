<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\FlattenByKondisi;
use App\Services\BacaMeterQueryService;

class KondisiBaca0 extends BaseController
{
    public function __construct(
        private readonly ?BacaMeterQueryService $queryService = null,
    ) {
    }

    private function getQueryService(): BacaMeterQueryService
    {
        return $this->queryService ?? new BacaMeterQueryService();
    }

    public function index()
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

        [$tglAwal, $tglAkhir] = get_start_end_date_from_year_month($req->tahun ?? null, $req->bulan ?? null);

        $data = $this->getQueryService()->getKondisiBaca0($tglAwal, $tglAkhir);

        $flattenKondisi = new FlattenByKondisi($data, $periode);
        $result = $flattenKondisi->get();
        $footer = $flattenKondisi->getFooter();

        return response()->setJSON([
            "rows" => $result,
            "total" => count($result),
            "footer" => $footer,
        ]);
    }
}
