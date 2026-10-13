<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\HasilBacaToExcel;
use App\Models\BacaMeterModel;
use CodeIgniter\HTTP\ResponseInterface;

class DetailHasilBaca extends BaseController
{
    public function index(): ResponseInterface
    {
        ini_set('memory_limit', "-1");
        $req = (object) request()->getGet();

        [$tglAwal, $tglAkhir] = get_start_end_date_from_periode($req->periode ?? null);
        $order = empty($req->order) ? "asc" : $req->order;
        $nosamw = isset($req->nosamw) && !empty($req->nosamw) ? $req->nosamw : null;
        $cabang = isset($req->cabang) && $req->cabang != "" ? $req->cabang : null;
        $petugas = isset($req->petugas) && !empty($req->petugas) ? $req->petugas : null;
        $kampung = isset($req->kampung) && !empty($req->kampung) ? $req->kampung : null;
        $kondisi = isset($req->kondisi) && !empty($req->kondisi) ? $req->kondisi : null;
        $cek = isset($req->cek) && !empty($req->cek) ? $req->cek : "0";

        $model = new BacaMeterModel();
        $data = $model->getDataVerifCursor(
            $tglAwal,
            $tglAkhir,
            (string) $cek,
            $order,
            $cabang,
            $petugas,
            $kampung,
            $kondisi,
            $nosamw,
        );

        $filename = "detail_hasil_baca_" . $req->periode . "-" . date('YmdHis');
        $toExcel = new HasilBacaToExcel($data, $filename);

        return $toExcel->download();
    }
}
