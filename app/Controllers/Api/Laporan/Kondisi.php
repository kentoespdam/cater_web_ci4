<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\FlattenByKondisi;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;
use App\Models\KondisiModel;
use App\Models\Sikompak\CabangModel;
use CodeIgniter\HTTP\ResponseInterface;

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

        $data = $this->find($tglAwal, $tglAkhir);

        $flattenKondisi = new FlattenByKondisi($data, $periode);
        $result = $flattenKondisi->get();
        $footer = $flattenKondisi->getFooter();

        return response()->setJSON([
            "rows" => $result,
            "total" => count($result),
            "footer" => $footer
        ]);
    }

    private function find($tglAwal, $tglAkhir)
    {
        $model = new BacaMeterModel();
        return $model->select("
                baca_meter.kondisi AS kondisi,
                COUNT(baca_meter.no_sam) AS total,
                munit.satker AS satker
            ")
            ->join('munit', 'SUBSTRING(baca_meter.no_sam,1,2) = munit.unit', 'inner')
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->groupBy('baca_meter.kondisi')
            ->groupBy("munit.satker")
            ->findAll();
    }
}
