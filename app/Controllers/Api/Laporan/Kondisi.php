<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\FlattenByKondisi;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;

class Kondisi extends BaseController
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

        $bulan = $req->bulan < 10 ? '0' . $req->bulan : $req->bulan;
        $periode = "{$req->tahun}-{$bulan}";

        $myDate = MyDate::withPeriode($periode);
        $tglAwal = $myDate->getStartDate();
        $tglAkhir = $myDate->getEndDate();

        $model = new BacaMeterModel();
        $data = $model->getKondisiBaca($tglAwal, $tglAkhir);

        $flattenKondisi = new FlattenByKondisi($data, $periode);
        $result = $flattenKondisi->get();
        $footer = $flattenKondisi->getFooter();

        return response()->setJSON([
            "rows" => $result,
            "total" => count($result),
            "footer" => $footer
        ]);
    }
}
