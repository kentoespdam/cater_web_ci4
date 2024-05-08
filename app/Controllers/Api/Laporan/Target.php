<?php

namespace App\Controllers\Api\Laporan;

use App\Controllers\BaseController;
use App\Libraries\MyDate;
use App\Models\BacaMeterModel;

class Target extends BaseController
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
        $myDate = MyDate::withDateYearAndMonth($req->tahun, $req->bulan);
        $tglAwal = $myDate->getStartDate();
        $tglAkhir = $myDate->getEndDate();
        $target = "{$req->tahun}-{$req->bulan}-25";

        $find = $this->find($tglAwal, $tglAkhir, $target);

        return response()->setJSON([
            "rows" => $this->generateResults($find, $periode),
            "total" => count($find),
            "footer" => $this->generateFooter($find),
        ]);
    }

    /**
     * Finds meter readings between two dates.
     *
     * @param string $tglAwal The start date in 'Y-m-d' format.
     * @param string $tglAkhir The end date in 'Y-m-d' format.
     * @return array<object> An array of meter reading objects.
     */
    private function find(string $tglAwal, string $tglAkhir, string $target): array
    {
        $bacaMeterModel = new BacaMeterModel();
        $result = $bacaMeterModel->select("
                COUNT(baca_meter.no_sam) AS jml_baca,
                SUM( CASE WHEN baca_meter.tgl <= '$target' THEN 1 ELSE 0 END ) AS sukses,
                SUM( CASE WHEN baca_meter.tgl > '$target' THEN 1 ELSE 0 END ) AS gagal,
                SUM( CASE WHEN baca_meter.kondisi NOT IN ('Normal', 'Edit Stand OCR') THEN 1 ELSE 0 END ) AS kondisi,
                mcabang.nm_cabang AS cabang,
            ")
            ->join("munit", "SUBSTRING(baca_meter.no_sam,1,2)=munit.unit")
            ->join("mcabang", "munit.satker=mcabang.id_cabang")
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->groupBy("munit.satker")
            ->findAll();
        // echo $bacaMeterModel->getLastQuery();
        return $result;
    }

    /**
     * Generates an array of meter reading objects with period and success rate.
     *
     * @param array<object> $meterReadings An array of meter reading objects.
     * @param string $period The period in 'Y-m-d' format.
     * @return array<object> An array of meter reading objects with period and success rate.
     */
    private function generateResults(array $meterReadings, string $period): array
    {
        return array_map(function (object $reading) use ($period): object {
            $successRate = (($reading->sukses - $reading->kondisi) / $reading->jml_baca) * 100;
            return (object) array_merge((array) $reading, [
                'periode' => $period,
                'persen' => number_format($successRate, 2)
            ]);
        }, $meterReadings);
    }

    private function generateFooter($find)
    {
        $reduce = array_reduce($find, function ($a, $b) {
            $a->jml_baca += $b->jml_baca;
            $a->sukses += $b->sukses;
            $a->gagal += $b->gagal;
            $a->kondisi += $b->kondisi;
            return $a;
        }, (object)[
            'cabang' => 'Total',
            'jml_baca' => 0,
            'sukses' => 0,
            'gagal' => 0,
            'kondisi' => 0
        ]);
        $persen = (($reduce->sukses - $reduce->kondisi) / $reduce->jml_baca) * 100;
        $reduce->persen = number_format($persen, 2);
        return [$reduce];
    }
}
