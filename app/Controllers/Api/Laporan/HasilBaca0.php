<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Services\BacaMeterQueryService;

class HasilBaca0 extends BaseController
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
        $page = isset($req->page) ? (int) $req->page : 1;
        $size = isset($req->rows) ? (int) $req->rows : 10;

        [$tglAwal, $tglAkhir] = get_start_end_date_from_year_month($req->tahun ?? null, $req->bulan ?? null);

        $result = $this->getHasilBaca0($page, $size, $tglAwal, $tglAkhir, $req->cabang);
        $total = $this->getTotalHasilBaca0($tglAwal, $tglAkhir, $req->cabang);

        return response()->setJSON([
            "rows" => $result,
            "total" => $total->total,
        ]);
    }

    private function getHasilBaca0($page, $size, $tglAwal, $tglAkhir, $cabang = null)
    {
        $offset = ($page - 1) * (int) $size;
        $result = $this->getQueryService()->getHasilBaca0($tglAwal, $tglAkhir, $cabang, $size, $offset);

        return $result;
    }

    private function getTotalHasilBaca0($tglAwal, $tglAkhir, $cabang = null)
    {
        $bacaMeterModel = new BacaMeterModel();
        $builder = $bacaMeterModel->select("COUNT(*) AS total")
            ->join("munit", "SUBSTRING( baca_meter.no_sam, 1, 2 )= munit.unit", "inner")
            ->join("mcabang", "munit.satker = mcabang.id_cabang", "inner")
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->where("baca_meter.pakai=0");

        if ($cabang && $cabang != "") {
            $builder->where("munit.satker", $cabang);
        }

        return $builder->first();
    }
}
