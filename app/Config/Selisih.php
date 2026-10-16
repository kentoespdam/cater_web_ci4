<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Selisih extends BaseConfig
{
    public string $appUrl = 'http://192.168.1.215:82/selisih/';

    public function __construct()
    {
        parent::__construct();

        $envUrl = env('SELISIH_APP_URL');

        if (!empty($envUrl)) {
            $this->appUrl = $envUrl;
        }
    }
}
