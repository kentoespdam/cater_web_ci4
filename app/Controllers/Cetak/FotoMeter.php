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
                nama, 
                alamat, 
                no_sam AS nosamw, 
                tgl, 
                info AS tgl_baca, 
                stan_kini, petugas, 
                CONCAT(folderSS,'|',fileSS) AS foto
            ")
            ->where("no_sam", $nosamw)
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
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
                "petugas" => $baca->petugas
            ];
        }, $data);

        return (object)$result;
    }
}
