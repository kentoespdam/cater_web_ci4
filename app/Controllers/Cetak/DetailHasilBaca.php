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
        ini_set('memory_limit', "-1");
        $req = (object)request()->getGet();

        // if (!isset($req->cabang) || empty($req->cabang)) {
        //     return response()->setJSON([
        //         "status" => "error",
        //         "message" => "Cabang harus dipilih"
        //     ]);
        // }

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

        // print_r($data);

        // header("Content-Type: application/vnd.ms-excel");
        // header('Content-Disposition: attachment;filename=' . $filename . '.xls');
        header("Content-Type: application/xls");
        header("Content-Disposition: attachment; filename=$filename.xls");
        header("Pragma: no-cache");
        header("Expires: 0");

        echo '
        <table border="1">
            <thead>
                <tr>
                    <th align="center">tgl</th>
                    <th align="center">no_sam</th>
                    <th align="center">stan_kini</th>
                    <th align="center">stan_lalu</th>
                    <th align="center">pakai</th>
                    <th align="center">petugas</th>
                    <th align="center">kondisi</th>
                    <th align="center">ket</th>
                    <th align="center">info</th>
                    <th align="center">ptgs_met</th>
                    <th align="center">rata</th>
                    <th align="center">kd_wil</th>
                    <th align="center">wil</th>
                    <th align="center">kd_cabang</th>
                    <th align="center">nm_cabang</th>
                    <th align="center">kd_cek</th>
                    <th align="center">status_cek</th>
                    <th align="center">nama</th>
                    <th align="center">alamat</th>
            </thead>
            <tbody>
        ';

        foreach ($data as $row) {
            echo '
            <tr>
                <td nowrap>' . $row->tgl . '</td>
                <td nowrap>\'' . $row->no_sam . '</td>
                <td nowrap>' . $row->stan_kini . '</td>
                <td nowrap>' . $row->stan_lalu . '</td>
                <td nowrap>' . $row->pakai . '</td>
                <td nowrap>' . $row->petugas . '</td>
                <td nowrap>' . $row->kondisi . '</td>
                <td nowrap>' . $row->ket . '</td>
                <td nowrap>' . $row->info . '</td>
                <td nowrap>\'' . $row->ptgs_met . '</td>
                <td nowrap>' . $row->rata . '</td>
                <td nowrap>\'' . $row->kd_wil . '</td>
                <td nowrap>' . $row->wil . '</td>
                <td nowrap>\'' . $row->kd_cabang . '</td>
                <td nowrap>' . $row->nm_cabang . '</td>
                <td nowrap>' . $row->kd_cek . '</td>
                <td nowrap>' . $row->status_cek . '</td>
                <td nowrap>' . $row->nama . '</td>
                <td nowrap>' . $row->alamat . '</td>
            </tr>
            ';
        }

        echo '
                </tbody>
        </table>
        ';

        // return \view("cetak/detail_hasil_baca", [
        //     "filename" => $filename,
        //     "data" => $data,
        //     // "data" => [],
        // ]);

        // print_r($data);

        // $toExcel = new HasilBacaToExcel($data, $filename);

        // return $toExcel->download();
    }
}
