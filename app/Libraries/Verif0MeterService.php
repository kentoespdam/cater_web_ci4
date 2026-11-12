<?php

namespace App\Libraries;

use App\Models\BacaMeterModel;
use App\Services\BacaMeterQueryService;

class Verif0MeterService
{
    public function __construct(
        private readonly ?BacaMeterQueryService $queryService = null,
    ) {
    }

    private function getQueryService(): BacaMeterQueryService
    {
        return $this->queryService ?? new BacaMeterQueryService();
    }

    private function getHasilBaca0(int $page, int $size, string $tglAwal, string $tglAkhir, string|null $cabang = null)
    {
        $offset = ($page - 1) * (int) $size;
        $result = $this->getQueryService()->getHasilBaca0($tglAwal, $tglAkhir, $cabang, $size, $offset);

        return $result;
    }

    private function getTotalHasilBaca0(string $tglAwal, string $tglAkhir, string|null $cabang = null)
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

        return $builder->first()->total ?? 0;
    }

    public function getVerif0MeterCater(array $filters = [])
    {
        $req = (object) $filters;
        $page = isset($req->page) ? (int) $req->page : 1;
        $size = isset($req->rows) ? (int) $req->rows : 10;

        [$tglAwal, $tglAkhir] = get_start_end_date_from_year_month($req->tahun ?? null, $req->bulan ?? null);

        return (object) [
            "result" => $this->getHasilBaca0($page, $size, $tglAwal, $tglAkhir, $req->cabang),
            "total" => $this->getTotalHasilBaca0($tglAwal, $tglAkhir, $req->cabang),
        ];
    }
}
