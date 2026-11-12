<?php

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;

class GrafikVerifikasi0 extends BaseController
{
    public function index(): string
    {
        $view = \Config\Services::renderer();

        return $view->setVar('session', session()->get('logged_in'))
            ->setVar('tahun', (int) date('Y'))
            ->render('laporan/grafik_verifikasi_0');
    }
}
