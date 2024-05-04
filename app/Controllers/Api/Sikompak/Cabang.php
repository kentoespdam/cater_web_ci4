<?php

namespace App\Controllers\Api\Sikompak;

use App\Controllers\BaseController;
use App\Models\Sikompak\CabangModel;
use CodeIgniter\HTTP\ResponseInterface;

class Cabang extends BaseController
{
    public function index()
    {
        $cabang = new CabangModel();
        $cabangData = array_map(fn ($item) =>
        [
            "label" => $item->nm_cabang,
            "value" => $item->id_cabang
        ], $cabang->orderBy("id_cabang", "asc")->findAll());

        return response()->setJSON($cabangData);
    }
}
