<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Libraries\KondisiToExcel;
use CodeIgniter\HTTP\DownloadResponse;
use CodeIgniter\Test\CIUnitTestCase;
use ZipArchive;

/**
 * @internal
 */
final class KondisiToExcelTest extends CIUnitTestCase
{
    public function testGeneratesValidXlsxFromData(): void
    {
        $data = [
            [
                'periode' => '2026-09',
                'cabang' => 'Cabang 1',
                'normal' => 10,
                'mati' => 2,
                'stand tunggu' => 1,
                'stand mundur' => 0,
                'ganti wm' => 0,
                'pencurian air' => 0,
                'tidak terjangkau' => 0,
                'berembun' => 0,
                'hilang' => 0,
                'rusak' => 1,
                'buram' => 0,
                'terpendam' => 0,
                'tempel angka' => 0,
                'pagar kunci' => 0,
                'segel putus' => 0,
                'cabut meter' => 0,
                'unknown' => 0,
                'total' => 14,
            ],
        ];

        $library = new KondisiToExcel($data, 'test_rekap_kondisi');
        $response = $library->download();

        $this->assertInstanceOf(DownloadResponse::class, $response);

        ob_start();
        $response->send();
        $content = ob_get_clean();
        $this->assertNotEmpty($content);

        $tempFile = tempnam(sys_get_temp_dir(), 'test_kondisi_xlsx_');
        file_put_contents($tempFile, $content);

        $zip = new ZipArchive();
        $res = $zip->open($tempFile);
        $this->assertTrue($res === true, 'Generated file is a valid ZIP archive.');

        $this->assertNotFalse($zip->locateName('[Content_Types].xml'));
        $this->assertNotFalse($zip->locateName('xl/worksheets/sheet1.xml'));

        $zip->close();
        @unlink($tempFile);
    }
}
