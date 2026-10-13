<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Libraries\HasilBacaToExcel;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\Test\CIUnitTestCase;
use ZipArchive;

/**
 * @internal
 */
final class HasilBacaToExcelTest extends CIUnitTestCase
{
    private function getSampleGenerator(array $rows): \Generator
    {
        foreach ($rows as $row) {
            yield $row;
        }
    }

    public function testGeneratesValidXlsxFromGenerator(): void
    {
        $data = [
            [
                'tgl' => '2026-09-01',
                'no_sam' => '0201001',
                'stan_kini' => 100,
                'stan_lalu' => 90,
                'pakai' => 0,
                'petugas' => 'Petugas 1',
                'kondisi' => 'normal',
                'ket' => '',
                'info' => '',
                'ptgs_met' => 'Wilayah 1',
                'rata' => 10,
                'kd_wil' => '02',
                'wil' => 'Wilayah Purwokerto',
                'kd_cabang' => '0200',
                'nm_cabang' => 'Cabang Purwokerto 1',
                'kd_cek' => 0,
                'status_cek' => 'Belum Cek',
                'nama' => 'Budi',
                'alamat' => 'Jl. Merdeka 1',
            ],
        ];

        $generator = $this->getSampleGenerator($data);
        $library = new HasilBacaToExcel($generator, 'test_hasil_baca');
        $response = $library->download();

        $this->assertInstanceOf(DownloadResponse::class, $response);

        ob_start();
        $response->send();
        $content = ob_get_clean();
        $this->assertNotEmpty($content);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_xlsx_');
        file_put_contents($tempFile, $content);

        $zip = new ZipArchive();
        $res = $zip->open($tempFile);
        $this->assertTrue($res === true, 'Generated file is a valid ZIP archive.');

        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));

        $zip->close();
        @unlink($tempFile);
    }

    public function testGeneratesValidXlsxFromEmptyData(): void
    {
        $generator = $this->getSampleGenerator([]);
        $library = new HasilBacaToExcel($generator, 'test_empty_baca');
        $response = $library->download();

        $this->assertInstanceOf(DownloadResponse::class, $response);

        ob_start();
        $response->send();
        $content = ob_get_clean();
        $this->assertNotEmpty($content);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_xlsx_empty_');
        file_put_contents($tempFile, $content);

        $zip = new ZipArchive();
        $res = $zip->open($tempFile);
        $this->assertTrue($res === true, 'Empty XLSX file is a valid ZIP archive.');
        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));

        $zip->close();
        @unlink($tempFile);
    }

    public function testHandlesAssociativeAndObjectItems(): void
    {
        $obj = new \stdClass();
        $obj->tgl = '2026-09-02';
        $obj->no_sam = '0201002';
        $obj->stan_kini = 150;
        $obj->stan_lalu = 140;
        $obj->pakai = 0;
        $obj->petugas = 'Petugas 2';
        $obj->kondisi = 'rusak';
        $obj->ket = 'Rusak meter';
        $obj->info = '';
        $obj->ptgs_met = 'Wilayah 2';
        $obj->rata = 10;
        $obj->kd_wil = '02';
        $obj->wil = 'Wilayah Purwokerto';
        $obj->kd_cabang = '0200';
        $obj->nm_cabang = 'Cabang Purwokerto 1';
        $obj->kd_cek = 0;
        $obj->status_cek = 'Belum Cek';
        $obj->nama = 'Ani';
        $obj->alamat = 'Jl. Merdeka 2';

        $data = [
            $obj,
            [
                'tgl' => '2026-09-03',
                'no_sam' => '0201003',
                'stan_kini' => 200,
                'stan_lalu' => 190,
                'pakai' => 0,
                'petugas' => 'Petugas 3',
                'kondisi' => 'normal',
                'ket' => '',
                'info' => '',
                'ptgs_met' => 'Wilayah 1',
                'rata' => 10,
                'kd_wil' => '02',
                'wil' => 'Wilayah Purwokerto',
                'kd_cabang' => '0200',
                'nm_cabang' => 'Cabang Purwokerto 1',
                'kd_cek' => 0,
                'status_cek' => 'Belum Cek',
                'nama' => 'Joko',
                'alamat' => 'Jl. Merdeka 3',
            ],
        ];

        $generator = $this->getSampleGenerator($data);
        $library = new HasilBacaToExcel($generator, 'test_mixed_baca');
        $response = $library->download();

        $this->assertInstanceOf(DownloadResponse::class, $response);

        ob_start();
        $response->send();
        $content = ob_get_clean();
        $this->assertNotEmpty($content);
    }
}
