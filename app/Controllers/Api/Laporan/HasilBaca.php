<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Models\BacaMeterModel;

class HasilBaca extends BaseController
{
    public function index()
    {
        $req = (object) request()->getGet();
        $page = empty($req->page) ? 0 : (int)$req->page;
        $size = empty($req->rows) ? 10 : (int)$req->rows;
        $sort = empty($req->sort) ? null : $req->sort;
        $order = empty($req->order) ? "asc" : $req->order;
        $nosamw = isset($req->nosamw) && !empty($req->nosamw) ? $req->nosamw : null;
        $date = date("Y-m");
        $skrng = !isset($req->periode) && empty($periode) ? new \DateTime("$date-01") : new \DateTime("$req->periode-01");
        $tglAwal = date("Y-m-d", $skrng->getTimestamp());
        $akhir = new \DateTime($skrng->format('Y-m-t'));
        $tglAkhir = date('Y-m-d', $akhir->getTimestamp());
        $cabang = isset($req->cabang) && !empty($req->cabang) ? $req->cabang : null;
        $petugas = isset($req->petugas) && !empty($req->petugas) ? $req->petugas : null;
        $kampung = isset($req->kampung) && !empty($req->kampung) ? $req->kampung : null;
        $kondisi = isset($req->kondisi) && !empty($req->kondisi) ? $req->kondisi : null;
        $cek =  isset($req->cek) && !empty($req->cek) ? $req->cek : "0";

        $bacaMeter = new BacaMeterModel();
        $dataVerif = $bacaMeter->getDataVerif(
            $page,
            $size,
            $tglAwal,
            $tglAkhir,
            $cek,
            $order,
            $sort,
            $cabang,
            $petugas,
            $kampung,
            $kondisi,
            $nosamw
        );
        $totalData = $bacaMeter->getTotalData(
            $tglAwal,
            $tglAkhir,
            $cek,
            $cabang,
            $petugas,
            $kampung,
            $kondisi,
            $nosamw
        );
        $result = [
            "rows" => $dataVerif,
            "total" => $totalData->total
        ];

        return response()->setJSON($result);
    }
}
