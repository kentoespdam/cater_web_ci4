<?php

declare(strict_types=1);

namespace Tests\Support;

use App\Services\HasilBacaSummaryService;
use CodeIgniter\Test\CIUnitTestCase;

final class HasilBacaSummaryServiceTest extends CIUnitTestCase
{
    public function testGenerateFooterCalculatesCorrectly(): void
    {
        $service = new HasilBacaSummaryService();
        $input = [
            [
                'jml_pelanggan' => 100,
                'jml_baca' => 80,
                'progress' => 80.00,
                'cek_koperasi' => 10,
                'cek_cabang' => 5,
                'gagal' => 2,
            ],
            [
                'jml_pelanggan' => 200,
                'jml_baca' => 150,
                'progress' => 75.00,
                'cek_koperasi' => 20,
                'cek_cabang' => 10,
                'gagal' => 5,
            ],
        ];

        $footer = $service->generateFooter($input);

        $this->assertSame(300, $footer['jml_pelanggan']);
        $this->assertSame(230, $footer['jml_baca']);
        $this->assertSame(155.00, $footer['progress']);
        $this->assertSame(30, $footer['cek_koperasi']);
        $this->assertSame(15, $footer['cek_cabang']);
        $this->assertSame(7, $footer['gagal']);
    }
}
