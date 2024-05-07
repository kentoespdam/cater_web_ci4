<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;
use CodeIgniter\HTTP\ResponseInterface;

class HasilBaca0 extends BaseController
{
    public function index()
    {
        $req = (object)request()->getGet();
        if (!isset($req->tahun) || !isset($req->bulan) || empty($req->tahun) || empty($req->bulan))
            return response()->setJSON([
                "rows" => [],
                "total" => 0,
                "footer" => null
            ]);
        $page = isset($req->page) ? (int)$req->page : 1;
        $size = isset($req->rows) ? (int)$req->rows : 10;

        $dateLib = MyDate::withDateYearAndMonth($req->tahun, $req->bulan);

        $tglAwal = $dateLib->getStartDate();
        $tglAkhir = $dateLib->getEndDate();

        $result = $this->getHasilBaca0($page, $size, $tglAwal, $tglAkhir, $req->cabang);
        $total = $this->getTotalHasilBaca0($tglAwal, $tglAkhir, $req->cabang);
        return response()->setJSON([
            "rows" => $result,
            "total" => $total->total,
        ]);
    }

    private function getHasilBaca0($page, $size, $tglAwal, $tglAkhir, $cabang = null)
    {
        $offset = ($page - 1) * (int)$size;
        $bacaMeterModel = new BacaMeterModel();
        $builder = $bacaMeterModel->select("
                baca_meter.no_sam AS nosamw,
                baca_meter.nama,
                baca_meter.alamat,
                baca_meter.stan_kini,
                baca_meter.stan_lalu,
                baca_meter.pakai,
                baca_meter.`user` AS petugas,
                baca_meter.kondisi,
                baca_meter.ket,
                baca_meter.info AS tgl_baca,
                baca_meter.cek,
                mcabang.nm_cabang 
            ")
            ->join("munit", "SUBSTRING( baca_meter.no_sam, 1, 2 )= munit.unit", "inner")
            ->join("mcabang", "munit.satker = mcabang.id_cabang", "inner")
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->where("baca_meter.pakai=0")
            ->limit($size, $offset);
        if ($cabang && $cabang != "")
            $builder->where("munit.satker", $cabang);

        $result = $builder->findAll();
        // echo $bacaMeterModel->getLastQuery();
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
        if ($cabang && $cabang != "")
            $builder->where("munit.satker", $cabang);

        return $builder->first();
    }
}
