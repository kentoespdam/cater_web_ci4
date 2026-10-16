<?php

namespace App\Controllers\Api;

use App\Controllers\BaseController;
use CodeIgniter\API\ResponseTrait;

class Selisih extends BaseController
{
    use ResponseTrait;

    private $defaultPropertyGridData = [
        "total" => 3,
        "rows" => [
            ["name" => "jmlData", "value" => 0],
            ["name" => "jmlFoto", "value" => 0],
            ["name" => "selisih", "value" => 0],
        ],
    ];

    public function index()
    {
        if (request()->getServer("QUERY_STRING") == "") {
            return response()->setJSON([
                "rows" => [],
                "total" => 0,
                "propertygrid" => $this->defaultPropertyGridData,
            ]);
        }

        $config = config(\Config\Selisih::class);
        $baseUrl = rtrim($config->appUrl ?: 'http://192.168.1.215:82/selisih/', '/') . '/';

        try {
            $client = \Config\Services::curlrequest([
                "baseURI" => $baseUrl,
                "timeout" => 15,
                "http_errors" => false,
            ]);

            $result = $client->get(
                "api.php",
                [
                    "query" => request()->getGet(),
                ],
            );

            if ($result->getStatusCode() !== 200) {
                return $this->fail('Gagal dari service Selisih, status: ' . $result->getStatusCode(), $result->getStatusCode());
            }

            $body = $result->getBody();
            $data = json_decode($body);

            if (json_last_error() !== JSON_ERROR_NONE || !is_object($data)) {
                return $this->failServerError('Format JSON dari service Selisih tidak valid.');
            }

            $rows = $data->data ?? [];
            $jmlData = $data->jmlData ?? 0;
            $jmlFoto = $data->jmlFoto ?? 0;
            $selisih = $data->selisih ?? 0;

            $responseResult = [
                "rows" => $rows,
                "total" => is_array($rows) ? count($rows) : 0,
                "propertygrid" => [
                    "total" => 3,
                    "rows" => [
                        ["name" => "jmlData", "value" => $jmlData],
                        ["name" => "jmlFoto", "value" => $jmlFoto],
                        ["name" => "selisih", "value" => $selisih],
                    ],
                ],
            ];

            return response()->setJSON($responseResult);

        } catch (\Throwable $e) {
            log_message('error', 'Selisih API error: ' . $e->getMessage());

            return $this->failServerError('Gagal terhubung ke service Selisih: ' . $e->getMessage());
        }
    }

    public function defaultPropertyGrid()
    {
        return response()->setJSON($this->defaultPropertyGridData);
    }
}
