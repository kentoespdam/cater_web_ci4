<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use App\Libraries\ImageToString;
use App\Models\BacaMeterModel;
use CodeIgniter\HTTP\ResponseInterface;

class FotoMeter extends BaseController
{
    public function index($tglAwal, $tglAkhir, $nosamw)
    {
        helper("bulan");
        $view = \Config\Services::renderer();
        $bacaMeterModel = new BacaMeterModel();
        $bacameter = $bacaMeterModel
            ->select("
                baca_meter.nama, 
                baca_meter.alamat, 
                baca_meter.no_sam AS nosamw, 
                baca_meter.tgl, 
                baca_meter.info AS tgl_baca, 
                baca_meter.stan_kini,
                baca_meter.pakai, 
                baca_meter.rata,
                users.nama_lengkap AS petugas, 
                CONCAT(baca_meter.folderSS,'|',baca_meter.fileSS) AS foto
            ")
            ->join("users", "baca_meter.user=users.username")
            ->where("baca_meter.no_sam", $nosamw)
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->findAll();
        $data = $this->resultBuilder($bacameter);
        return $view->setVar("data", $data)
            ->render("cetak/fotometer");
    }

    private function resultBuilder(array $data): object
    {
        $result = [
            "nama" => $data[0]->nama,
            "alamat" => $data[0]->alamat,
            "nosamw" => $data[0]->nosamw,
        ];

        $result["rows"] = array_map(function ($baca) {
            $imgToStr = new ImageToString($baca->foto);
            return (object)[
                "foto" => $imgToStr->get(),
                "tgl" => $baca->tgl,
                "tgl_baca" => $baca->tgl_baca,
                "stan_kini" => $baca->stan_kini,
                "pakai" => $baca->pakai,
                "rata" => $baca->rata,
                "petugas" => $baca->petugas
            ];
        }, $data);

        return (object)$result;
    }
}
