<?php

namespace App\Controllers\Cetak;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class SelisihFoto extends BaseController
{
    private $defaultPropertyGridData = [
        "total" => 3,
        "rows" => [
            ["name" => "jmlData", "value" => 0],
            ["name" => "jmlFoto", "value" => 0],
            ["name" => "selisih", "value" => 0]
        ]
    ];

    public function index()
    {
        helper("bulan");
        $view = \Config\Services::renderer();
        return $view
            ->setVar("data", $this->getData())
            ->render("cetak/selisih");
    }

    private function getData(): object
    {
        if (request()->getServer("QUERY_STRING") == "")
            return response()->setJSON([
                "rows" => [],
                "total" => 0,
                "propertygrid" => $this->defaultPropertyGridData
            ]);

        $client = \Config\Services::curlrequest([
            "baseURI" => "http://192.168.1.215:82/selisih/"
        ]);

        $result = $client->get(
            "api.php",
            [
                "query" => request()->getGet()
            ]
        );
        $data = json_decode($result->getBody());
        return (object)[
            "rows" => $data->data,
            "total" => count($data->data),
            "propertygrid" => (object)[
                "total" => 3,
                "rows" => [
                    (object)["name" => "jmlData", "value" => $data->jmlData],
                    (object)["name" => "jmlFoto", "value" => $data->jmlFoto],
                    (object)["name" => "selisih", "value" => $data->selisih]
                ]
            ]
        ];
    }
}
