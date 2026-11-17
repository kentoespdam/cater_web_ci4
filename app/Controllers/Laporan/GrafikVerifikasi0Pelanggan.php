<?php

declare(strict_types=1);

namespace App\Controllers\Laporan;

use App\Controllers\BaseController;

final class GrafikVerifikasi0Pelanggan extends BaseController
{
    public function index(): string
    {
        return \Config\Services::renderer()
            ->setVar('session', session()->get('logged_in'))
            ->setVar('tahun', (int) (filter_var($this->request->getGet('tahun'), FILTER_VALIDATE_INT) ?: date('Y')))
            ->render('laporan/grafik_verifikasi_0_pelanggan');
    }
}
