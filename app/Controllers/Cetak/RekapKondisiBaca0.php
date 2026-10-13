<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\FlattenByKondisi;
use App\Libraries\KondisiToExcel;
use App\Models\BacaMeterModel;

class RekapKondisiBaca0 extends BaseController
{
    public function index()
    {
        $req = (object) request()->getGet();
        [$tglAwal, $tglAkhir] = get_start_end_date_from_year_month($req->tahun ?? null, $req->bulan ?? null);
        $bulan = $req->bulan < 10 ? '0' . $req->bulan : $req->bulan;
        $periode = "{$req->tahun}-{$bulan}";


        $bacaMeterModel = new BacaMeterModel();

        $data = $bacaMeterModel->getKondisiBaca0($tglAwal, $tglAkhir);
        $flattenKondisi = new FlattenByKondisi($data, $periode);
        $result = $flattenKondisi->get();

        $filename = "rekap_kondisi_baca_0-" . date('YmdHis');
        $toExcel = new KondisiToExcel($result, $filename);

        return $toExcel->download();
    }
}
