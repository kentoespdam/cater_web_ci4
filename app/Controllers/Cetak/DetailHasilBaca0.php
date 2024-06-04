<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\HasilBacaToExcel;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;

class DetailHasilBaca0 extends BaseController
{
    public function index()
    {
        $req = (object)request()->getGet();
        $dateLib = MyDate::withDateYearAndMonth($req->tahun, $req->bulan);

        $tglAwal = $dateLib->getStartDate();
        $tglAkhir = $dateLib->getEndDate();
        $cabang = isset($req->cabang) && $req->cabang != "" ? $req->cabang : null;

        $bacaMeterModel = new BacaMeterModel();

        $data = $bacaMeterModel->getHasilBaca0($tglAwal, $tglAkhir, $cabang);
        $filename = "hasil_baca_0_" . date('YmdHis');
        $toExcel = new HasilBacaToExcel($data, $filename);

        return $toExcel->download();
    }
}
