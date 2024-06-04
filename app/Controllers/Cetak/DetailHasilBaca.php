<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\HasilBacaToExcel;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;

class DetailHasilBaca extends BaseController
{
    public function index()
    {
        $req = (object)request()->getGet();
        if (!isset($req->periode) && $req->periode = "") exit();

        $dateLib = MyDate::withPeriode($req->periode);

        $tglAwal = $dateLib->getStartDate();
        $tglAkhir = $dateLib->getEndDate();
        $order = empty($req->order) ? "asc" : $req->order;
        $nosamw = isset($req->nosamw) && !empty($req->nosamw) ? $req->nosamw : null;
        $cabang = isset($req->cabang) && $req->cabang != "" ? $req->cabang : null;
        $petugas = isset($req->petugas) && !empty($req->petugas) ? $req->petugas : null;
        $kampung = isset($req->kampung) && !empty($req->kampung) ? $req->kampung : null;
        $kondisi = isset($req->kondisi) && !empty($req->kondisi) ? $req->kondisi : null;
        $cek =  isset($req->cek) && !empty($req->cek) ? $req->cek : 0;

        $model = new BacaMeterModel();
        $data = $model->getDataVerif(
            $tglAwal,
            $tglAkhir,
            $cek,
            $order,
            null,
            null,
            null,
            $cabang,
            $petugas,
            $kampung,
            $kondisi,
            $nosamw
        );

        $filename = "detail_hasil_baca_" . $req->periode . "-" . date('YmdHis');

        $toExcel = new HasilBacaToExcel($data, $filename);

        return $toExcel->download();
    }
}
