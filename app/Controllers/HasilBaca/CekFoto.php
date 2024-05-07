<?php

namespace App\Controllers\Hasilbaca;

use App\Controllers\BaseController;
use CodeIgniter\HTTP\ResponseInterface;

class CekFoto extends BaseController
{
    public function index()
    {
        $view = \Config\Services::renderer();
        $session = session()->get("logged_in");

        return $view->setVar("session", $session)
        ->render("hasilbaca/cekfoto");
    }
}
