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

        header("Content-Type: application/vnd.ms-excel");
        header('Content-Disposition: attachment;filename=' . $filename . '.xls');
        header('Cache-Control: max-age=0');

        echo '
        <table class="table table-auto border-collapse" border="1">
            <thead>
                <tr>
                    <th class="border border-slate-300 p-2" align="center">tgl</th>
                    <th class="border border-slate-300 p-2" align="center">no_sam</th>
                    <th class="border border-slate-300 p-2" align="center">stan_kini</th>
                    <th class="border border-slate-300 p-2" align="center">stan_lalu</th>
                    <th class="border border-slate-300 p-2" align="center">pakai</th>
                    <th class="border border-slate-300 p-2" align="center">petugas</th>
                    <th class="border border-slate-300 p-2" align="center">kondisi</th>
                    <th class="border border-slate-300 p-2" align="center">ket</th>
                    <th class="border border-slate-300 p-2" align="center">info</th>
                    <th class="border border-slate-300 p-2" align="center">ptgs_met</th>
                    <th class="border border-slate-300 p-2" align="center">rata</th>
                    <th class="border border-slate-300 p-2" align="center">kd_wil</th>
                    <th class="border border-slate-300 p-2" align="center">wil</th>
                    <th class="border border-slate-300 p-2" align="center">kd_cabang</th>
                    <th class="border border-slate-300 p-2" align="center">nm_cabang</th>
                    <th class="border border-slate-300 p-2" align="center">kd_cek</th>
                    <th class="border border-slate-300 p-2" align="center">status_cek</th>
                    <th class="border border-slate-300 p-2" align="center">nama</th>
                    <th class="border border-slate-300 p-2" align="center">alamat</th>
            </thead>
            <tbody>
        ';

        foreach ($data as $row) {
            echo '
            <tr>
                <td class="border border-slate-300 p-2" nowrap>' . $row->tgl . '</td>
                <td class="border border-slate-300 p-2" nowrap>\'' . $row->no_sam . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->stan_kini . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->stan_lalu . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->pakai . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->petugas . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->kondisi . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->ket . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->info . '</td>
                <td class="border border-slate-300 p-2" nowrap>\'' . $row->ptgs_met . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->rata . '</td>
                <td class="border border-slate-300 p-2" nowrap>\'' . $row->kd_wil . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->wil . '</td>
                <td class="border border-slate-300 p-2" nowrap>\'' . $row->kd_cabang . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->nm_cabang . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->kd_cek . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->status_cek . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->nama . '</td>
                <td class="border border-slate-300 p-2" nowrap>' . $row->alamat . '</td>
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
