<?php

namespace App\Controllers\Api;

use App\Models\BacaMeterModel;
use App\Models\Sikompak\CustModel;
// use CodeIgniter\API\ResponseTrait;
use CodeIgniter\Controller;

class HasilBaca extends Controller
{
    // use ResponseTrait;
    public function index()
    {
        $date = date("Y-m");
        $periode = request()->getGet("periode");
        $skrng = empty($periode) ? new \DateTime("$date-01") : new \DateTime("$periode-01");
        $tglAwal = date("Y-m-d", $skrng->getTimestamp());
        $akhir = new \DateTime($skrng->format('Y-m-t'));
        $tglAkhir = date('Y-m-d', $akhir->getTimestamp());

        $custModel = new CustModel();
        $hasilBacaModel = new BacaMeterModel();

        $custData = $custModel->getTotalPelangganPerPembaca();
        $hasilBacaData = $hasilBacaModel->getJumlahCekData($tglAwal, $tglAkhir);
        $result = $this->generateResult($custData, $hasilBacaData);
        return response()
            ->setJSON([
                "total" => count($result),
                "rows" => $result,
            ]);
    }

    private function generateResult($cust, $hasilBaca)
    {
        return array_values(
            array_map(function ($item) use ($hasilBaca) {
                $curHasilBaca = $this->getCurrentHasilBaca($hasilBaca, $item->petugas);
                if ($curHasilBaca == null) return [
                    "petugas" => $item->petugas,
                    "nama" => $item->nama,
                    "cabang" => $item->cabang,
                    "jml_pelanggan" => $item->jml_pelanggan,
                    "jml_baca" => 0,
                    "cek_koperasi" => 0,
                    "cek_cabang" => 0,
                    "gagal" => 0,
                    "progress" => 0,
                ];
                $persen = ($curHasilBaca->jml_baca / $item->jml_pelanggan) * 100;
                return [
                    "petugas" => $item->petugas,
                    "nama" => $item->nama,
                    "cabang" => $item->cabang,
                    "jml_pelanggan" => $item->jml_pelanggan,
                    "jml_baca" => $curHasilBaca->jml_baca,
                    "cek_koperasi" => $curHasilBaca->cek_koperasi,
                    "cek_cabang" => $curHasilBaca->cek_cabang,
                    "gagal" => $curHasilBaca->gagal,
                    "progress" => number_format($persen, 2)
                ];
            }, $cust)
        );
    }

    private function getCurrentHasilBaca($hasilBaca, $petugas)
    {
        $values = array_values(
            array_filter($hasilBaca, function ($item) use ($petugas) {
                return $item->petugas == $petugas;
            })
        );
        return count($values) == 0 ? null : (object)$values[0];
    }

    public function getDataGagal()
    {
        $req = (object)request()->getGet();
        if (!isset($req->petugas) || !isset($req->periode) || empty($req->petugas) || empty($req->periode))
            return response()->setJSON([]);
        $skrng = new \DateTime("$req->periode-01");
        $tglAwal = date("Y-m-d", $skrng->getTimestamp());
        $akhir = new \DateTime($skrng->format('Y-m-t'));
        $tglAkhir = date('Y-m-d', $akhir->getTimestamp());

        $hasilBacaModel = new BacaMeterModel();
        $custModel = new CustModel();

        $hasilBacaData = $hasilBacaModel->getDataGagalPerUser($tglAwal, $tglAkhir, $req->petugas);
        $noPelanggan = array_map(function ($item) {
            return $item->nosamw;
        }, $hasilBacaData);
        $custData = $custModel->getDetailKampungPelanggan($noPelanggan);

        return response()->setJSON([
            "id" => $req->petugas,
            "total" => count($custData),
            "rows" => $custData
        ]);
    }
}
