<?php

declare(strict_types=1);

namespace Tests\App\Controllers\Api\Laporan;

use App\Controllers\Api\Laporan\GrafikVerifikasi0;
use App\Services\Verif0MeterReconciliationService;
use CodeIgniter\Config\Services;
use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\UserAgent;
use CodeIgniter\Test\CIUnitTestCase;

final class GrafikVerifikasi0Test extends CIUnitTestCase
{
    private string $tempSnapshotDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempSnapshotDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'verif0_ctrl_' . bin2hex(random_bytes(6)) . DIRECTORY_SEPARATOR;
        mkdir($this->tempSnapshotDir, 0755, true);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        if (is_dir($this->tempSnapshotDir)) {
            $files = scandir($this->tempSnapshotDir) ?: [];

            foreach ($files as $file) {
                if ($file !== '.' && $file !== '..') {
                    $fullPath = $this->tempSnapshotDir . $file;

                    if (is_file($fullPath)) {
                        unlink($fullPath);
                    }
                }
            }
            rmdir($this->tempSnapshotDir);
        }
    }

    private function createController(array $getParams = []): GrafikVerifikasi0
    {
        $request = new IncomingRequest(
            config('App'),
            new \CodeIgniter\HTTP\URI('http://example.com/api/laporan/grafik_verifikasi_0'),
            null,
            new UserAgent(),
        );
        $request->setGlobal('get', $getParams);

        $response = Services::response();
        $logger = Services::logger();

        $service = new Verif0MeterReconciliationService(snapshotDir: $this->tempSnapshotDir);
        $controller = new GrafikVerifikasi0($service);
        $controller->initController($request, $response, $logger);

        return $controller;
    }

    public function testIndexReturns400OnInvalidYear(): void
    {
        $controller = $this->createController(['tahun' => 'invalid']);
        $response = $controller->index();

        $this->assertSame(400, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertSame('error', $body['status']);
        $this->assertStringContainsString('Tahun tidak valid', $body['message']);
    }

    public function testIndexReturns404WhenSnapshotNotFound(): void
    {
        $controller = $this->createController(['tahun' => '2024']);
        $response = $controller->index();

        $this->assertSame(404, $response->getStatusCode());
        $body = json_decode($response->getBody(), true);
        $this->assertSame('not_found', $body['status']);
        $this->assertStringContainsString('belum tersedia', $body['message']);
    }

    public function testIndexReturns200WithSnapshotJsonWhenExists(): void
    {
        $snapshotFile = $this->tempSnapshotDir . 'verif_0meter_2026.json';
        $payload = [
            'tahun' => 2026,
            'updated_at' => '2026-11-17 14:00:00',
            'source_status' => 'complete',
            'cabang' => [
                [
                    'id' => '0200',
                    'nama' => 'Cabang Purwokerto 1',
                    'target_bulan_ini' => [10, 20],
                    'verifikasi_bulan_ini' => [5, 10],
                    'verifikasi_kumulatif' => [5, 15],
                ],
            ],
        ];
        file_put_contents($snapshotFile, json_encode($payload));

        $controller = $this->createController(['tahun' => '2026']);
        $response = $controller->index();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertStringStartsWith('application/json', $response->getHeaderLine('Content-Type'));
        $body = json_decode($response->getBody(), true);
        $this->assertSame(2026, $body['tahun']);
        $this->assertSame('complete', $body['source_status']);
        $this->assertCount(1, $body['cabang']);
    }
}
