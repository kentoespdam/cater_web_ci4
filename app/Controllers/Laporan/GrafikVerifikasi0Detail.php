<?php

declare(strict_types=1);

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;

final class GrafikVerifikasi0Detail extends BaseController
{
    public function index(): string
    {
        return \Config\Services::renderer()
            ->setVar('session', session()->get('logged_in'))
            ->setVar('tahun', (int) date('Y'))
            ->render('laporan/grafik_verifikasi_0_detail');
    }
}
