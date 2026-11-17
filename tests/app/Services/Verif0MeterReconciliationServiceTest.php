<?php

declare(strict_types=1);

namespace Tests\App\Services;

use App\Services\Verif0MeterReconciliationService;
use CodeIgniter\Test\CIUnitTestCase;

final class Verif0MeterReconciliationServiceTest extends CIUnitTestCase
{
    private string $tempSnapshotDir;

    protected function setUp(): void
    {
        parent::setUp();
        $this->tempSnapshotDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'verif0_test_' . bin2hex(random_bytes(6)) . DIRECTORY_SEPARATOR;
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

    public function testReconcileMatchesExactCustomerIds(): void
    {
        $service = new Verif0MeterReconciliationService(snapshotDir: $this->tempSnapshotDir);

        $unitCabangMap = [
            '01' => ['unit' => '01', 'cabang' => '0200', 'nama_cabang' => 'Cabang Purwokerto 1'],
        ];

        // Bulan 1 (Januari) target: pelanggan 0100001 dan 0100002
        $targetsByMonth = [
            1 => [
                '0100001' => '01',
                '0100002' => '01',
            ],
        ];

        // Verifikasi pada bulan Januari: hanya 0100001 yang diverifikasi
        $verifications = [
            [
                'nosamw' => '0100001',
                'tgl_verif' => '2026-01-15 10:00:00',
                'status' => 0,
            ],
        ];

        $result = $service->reconcile(2026, $targetsByMonth, $verifications, $unitCabangMap);

        $this->assertSame('complete', $result['source_status']);
        $cabang = $result['cabang'][0];
        $this->assertSame('0200', $cabang['id']);
        $this->assertSame(2, $cabang['target_awal_bulan'][0]);
        $this->assertSame(0, $cabang['terverifikasi_sebelum_bulan_ini'][0]);
        $this->assertSame(2, $cabang['target_bulan_ini'][0]);
        $this->assertSame(1, $cabang['realisasi_bulan_ini'][0]);
        $this->assertSame(1, $cabang['verifikasi_bulan_ini'][0]);
        $this->assertSame(1, $cabang['verifikasi_kumulatif'][0]);
    }

    public function testReconcileIgnoresUnmatchedVerifications(): void
    {
        $service = new Verif0MeterReconciliationService(snapshotDir: $this->tempSnapshotDir);

        $unitCabangMap = [
            '01' => ['unit' => '01', 'cabang' => '0200', 'nama_cabang' => 'Cabang Purwokerto 1'],
        ];

        // Target Januari: 0100001
        $targetsByMonth = [
            1 => ['0100001' => '01'],
        ];

        // Verifikasi Januari: dilakukan pada 0199999 (bukan target)
        $verifications = [
            [
                'nosamw' => '0199999',
                'tgl_verif' => '2026-01-20 14:00:00',
                'status' => 0,
            ],
        ];

        $result = $service->reconcile(2026, $targetsByMonth, $verifications, $unitCabangMap);
        $cabang = $result['cabang'][0];

        $this->assertSame(1, $cabang['target_awal_bulan'][0]);
        $this->assertSame(0, $cabang['realisasi_bulan_ini'][0]);
        $this->assertSame(0, $cabang['verifikasi_bulan_ini'][0]);
        $this->assertSame(1, $cabang['aktivitas_verifikasi_mentah'][0]);
    }

    public function testReconcileHandlesPreVerifications(): void
    {
        $service = new Verif0MeterReconciliationService(snapshotDir: $this->tempSnapshotDir);

        $unitCabangMap = [
            '01' => ['unit' => '01', 'cabang' => '0200', 'nama_cabang' => 'Cabang Purwokerto 1'],
        ];

        // Target Januari 2026: 0100001 dan 0100002
        $targetsByMonth = [
            1 => [
                '0100001' => '01',
                '0100002' => '01',
            ],
        ];

        // 0100001 sudah diverifikasi pada Desember 2025 (sebelum 1 Januari 2026)
        $verifications = [
            [
                'nosamw' => '0100001',
                'tgl_verif' => '2025-12-20 09:00:00',
                'status' => 0,
            ],
        ];

        $result = $service->reconcile(2026, $targetsByMonth, $verifications, $unitCabangMap);
        $cabang = $result['cabang'][0];

        $this->assertSame(2, $cabang['target_awal_bulan'][0]);
        $this->assertSame(1, $cabang['terverifikasi_sebelum_bulan_ini'][0]);
        $this->assertSame(1, $cabang['target_bulan_ini'][0]);
        $this->assertSame(0, $cabang['realisasi_bulan_ini'][0]);
    }

    public function testReconcileDeduplicatesMultipleVerifications(): void
    {
        $service = new Verif0MeterReconciliationService(snapshotDir: $this->tempSnapshotDir);

        $unitCabangMap = [
            '01' => ['unit' => '01', 'cabang' => '0200', 'nama_cabang' => 'Cabang Purwokerto 1'],
        ];

        $targetsByMonth = [
            1 => ['0100001' => '01'],
        ];

        // 0100001 diverifikasi dua kali pada bulan Januari 2026
        $verifications = [
            [
                'nosamw' => '0100001',
                'tgl_verif' => '2026-01-05 10:00:00',
                'status' => 0,
            ],
            [
                'nosamw' => '0100001',
                'tgl_verif' => '2026-01-20 11:00:00',
                'status' => 0,
            ],
        ];

        $result = $service->reconcile(2026, $targetsByMonth, $verifications, $unitCabangMap);
        $cabang = $result['cabang'][0];

        // Realisasi tetap 1 meskipun diverifikasi 2 kali
        $this->assertSame(1, $cabang['realisasi_bulan_ini'][0]);
        // Aktivitas mentah mencatat 2 baris verifikasi
        $this->assertSame(2, $cabang['aktivitas_verifikasi_mentah'][0]);
    }

    public function testLockPreventsConcurrentExecution(): void
    {
        $service = new Verif0MeterReconciliationService(snapshotDir: $this->tempSnapshotDir);
        $lockFile = $this->tempSnapshotDir . '.verif_0meter_2026.lock';

        $fp = fopen($lockFile, 'c+');
        $this->assertNotFalse($fp);
        flock($fp, LOCK_EX);

        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('sedang berjalan pada proses lain');

        try {
            $service->generate(2026);
        } finally {
            flock($fp, LOCK_UN);
            fclose($fp);
        }
    }

    public function testAtomicPublishPreservesPreviousSnapshotOnFailure(): void
    {
        $service = new Verif0MeterReconciliationService(snapshotDir: $this->tempSnapshotDir);
        $snapshotFile = $service->getSnapshotPath(2026);

        // Buat file snapshot valid sebelumnya
        $initialContent = json_encode(['tahun' => 2026, 'status' => 'old_snapshot']);
        file_put_contents($snapshotFile, $initialContent);

        // Simulasikan kegagalan validasi tahun (misal tahun invalid)
        try {
            $service->generate(1999);
        } catch (\RuntimeException) {
            // Expected
        }

        // Pastikan snapshot awal tidak berubah atau terhapus
        $this->assertFileExists($snapshotFile);
        $this->assertSame($initialContent, file_get_contents($snapshotFile));

        // Pastikan tidak ada file temporary tersisa
        $tmpFiles = array_filter(scandir($this->tempSnapshotDir) ?: [], fn ($f) => str_contains($f, '.tmp.'));
        $this->assertEmpty($tmpFiles);
    }
}
