<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\HasilBacaToExcel;
use App\Services\BacaMeterQueryService;
use CodeIgniter\HTTP\ResponseInterface;

class DetailHasilBaca0 extends BaseController
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
        [$tglAwal, $tglAkhir] = get_start_end_date_from_year_month($req->tahun ?? null, $req->bulan ?? null);
        $cabang = isset($req->cabang) && $req->cabang != "" ? $req->cabang : null;

        $data = $this->getQueryService()->getHasilBaca0Cursor($tglAwal, $tglAkhir, $cabang);
        $filename = "hasil_baca_0_" . date('YmdHis');
        $toExcel = new HasilBacaToExcel($data, $filename);

        return $toExcel->download();
    }
}
