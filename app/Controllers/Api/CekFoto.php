<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use App\Libraries\ImageToString;
use App\Models\BacaMeterModel;

class CekFoto extends BaseController
{
    public function index()
    {
        $req = (object)request()->getGet();
        if (!isset($req->nosamw) || !isset($req->tglAwal) || !isset($req->tglAkhir) || empty($req->nosamw) || empty($req->tglAwal) || empty($req->tglAkhir))
            return response()->setJSON([]);
        $bacaMeterModel = new BacaMeterModel();
        $data = $bacaMeterModel->cekFoto($req->nosamw, $req->tglAwal, $req->tglAkhir);
        return response()->setJSON([
            "rows" => $data,
            "total" => count($data)
        ]);
    }

    public function detail($id)
    {
        $bacaMeterModel = new BacaMeterModel();
        $data = $bacaMeterModel
            ->select("no AS id, tgl, info,stan_kini, CONCAT(folderSS,'|',fileSS) AS foto")
            ->find($id);
        $imgToString = new ImageToString($data->foto);
        $data->foto = $imgToString->get();
        return response()->setJSON([$data]);
    }
}
