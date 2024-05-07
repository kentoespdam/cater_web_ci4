<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;

class Selisih extends BaseController
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
        $result = [
            "rows" => $data->data,
            "total" => count($data->data),
            "propertygrid" => [
                "total" => 3,
                "rows" => [
                    ["name" => "jmlData", "value" => $data->jmlData],
                    ["name" => "jmlFoto", "value" => $data->jmlFoto],
                    ["name" => "selisih", "value" => $data->selisih]
                ]
            ]
        ];
        return response()->setJSON($result);
    }

    public function defaultPropertyGrid()
    {
        return response()->setJSON($this->defaultPropertyGridData);
    }
}
