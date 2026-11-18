<?php

declare(strict_types=1);

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\Verif0MeterService;
use CodeIgniter\HTTP\ResponseInterface;

class HasilBaca0 extends BaseController
{
    protected Verif0MeterService $verif0MeterService;

    public function __construct(
        private readonly ?Verif0MeterService $v0ms = null,
    ) {
        $this->verif0MeterService = $v0ms ?? new Verif0MeterService();
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

        $data = $this->verif0MeterService->getVerif0MeterCater(request()->getGet());

        return response()->setJSON([
            "rows" => $data->result,
            "total" => $data->total,
        ]);
    }
}
