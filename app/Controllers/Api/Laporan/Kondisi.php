<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\FlattenByKondisi;
use App\Services\BacaMeterQueryService;

class Kondisi extends BaseController
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

        [$tglAwal, $tglAkhir] = get_start_end_date_from_periode($periode);

        $data = $this->getQueryService()->getKondisiBaca($tglAwal, $tglAkhir);

        $flattenKondisi = new FlattenByKondisi($data, $periode);
        $result = $flattenKondisi->get();
        $footer = $flattenKondisi->getFooter();

        // echo "<pre>";
        // print_r($result);
        // echo "</pre>";
        // exit;

        return response()->setJSON([
            "rows" => $result,
            "total" => count($result),
            "footer" => $footer,
        ]);
    }
}
