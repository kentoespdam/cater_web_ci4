<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Verif0MeterService;
use App\Services\BacaMeterQueryService;

class HasilBaca0 extends BaseController
{
    protected $queryService;
    protected $verif0MeterService;

    public function __construct(
        private readonly ?BacaMeterQueryService $qs = null,
        private readonly ?Verif0MeterService $v0ms = null,
    ) {
        $this->queryService = $qs ?? new BacaMeterQueryService();
        $this->verif0MeterService =  $v0ms ?? new Verif0MeterService();
    }

    // private function getQueryService(): BacaMeterQueryService
    // {

    // }

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

        $data = $this->verif0MeterService->getVerif0MeterCater(request()->getGet());

        return response()->setJSON([
            "rows" => $data->result,
            "total" => $data->total,
        ]);
    }
}
