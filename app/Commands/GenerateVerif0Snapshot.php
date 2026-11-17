<?php

declare(strict_types=1);

namespace App\Commands;

use App\Services\Verif0MeterReconciliationService;
use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;

class GenerateVerif0Snapshot extends BaseCommand
{
    protected $group = 'Laporan';
    protected $name = 'laporan:generate-verif0-snapshot';
    protected $description = 'Membangun snapshot rekonsiliasi verifikasi 0 meter berbasis ID pelanggan.';
    protected $usage = 'laporan:generate-verif0-snapshot [options]';
    protected $options = [
        '--tahun' => 'Tahun laporan (default: tahun saat ini)',
    ];

    public function run(array $params)
    {
        $tahunOption = CLI::getOption('tahun');
        $tahun = $tahunOption !== null ? (int) $tahunOption : (int) date('Y');

        if ($tahun < 2000 || $tahun > 2100) {
            CLI::error("Tahun tidak valid: {$tahun}. Gunakan rentang 2000-2100.");

            return 1;
        }

        CLI::write("Memulai rekonsiliasi verifikasi 0 meter untuk tahun {$tahun}...", 'yellow');
        $startTime = microtime(true);

        try {
            $service = new Verif0MeterReconciliationService();
            $result = $service->generate($tahun);
            $duration = round(microtime(true) - $startTime, 2);

            $snapshotPath = $service->getSnapshotPath($tahun);
            $totalCabang = count($result['cabang'] ?? []);

            CLI::write("✓ Snapshot berhasil diterbitkan ke: {$snapshotPath}", 'green');
            CLI::write("  Tahun: {$result['tahun']} | Cabang: {$totalCabang} | Durasi: {$duration}s", 'cyan');

            return 0;
        } catch (\Throwable $e) {
            CLI::error('Gagal membangun snapshot: ' . $e->getMessage());
            log_message('error', '[GenerateVerif0Snapshot] ' . $e->getMessage());

            return 1;
        }
    }
}
