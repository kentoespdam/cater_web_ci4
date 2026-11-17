<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use RuntimeException;

final class Verif0MeterReconciliationService
{
    private readonly BaseConnection $dbDefault;
    private readonly BaseConnection $dbGis;
    private readonly string $snapshotDir;

    public function __construct(
        ?BaseConnection $dbDefault = null,
        ?BaseConnection $dbGis = null,
        ?string $snapshotDir = null,
    ) {
        $this->dbDefault = $dbDefault ?? Database::connect('default');
        $this->dbGis = $dbGis ?? Database::connect('gis');
        $this->snapshotDir = rtrim($snapshotDir ?? (defined('WRITEPATH') ? WRITEPATH . 'snapshots/' : '/tmp/snapshots/'), '/\\') . DIRECTORY_SEPARATOR;
    }

    public function getSnapshotPath(int $tahun): string
    {
        return $this->snapshotDir . sprintf('verif_0meter_%04d.json', $tahun);
    }

    public function generate(int $tahun): array
    {
        if ($tahun < 2000 || $tahun > 2100) {
            throw new RuntimeException("Tahun {$tahun} di luar rentang valid (2000-2100).");
        }

        if (! is_dir($this->snapshotDir) && ! mkdir($this->snapshotDir, 0755, true) && ! is_dir($this->snapshotDir)) {
            throw new RuntimeException("Gagal membuat direktori snapshot: {$this->snapshotDir}");
        }

        $lockFile = $this->snapshotDir . sprintf('.verif_0meter_%04d.lock', $tahun);
        $lockHandle = fopen($lockFile, 'c+');

        if ($lockHandle === false || ! flock($lockHandle, LOCK_EX | LOCK_NB)) {
            if ($lockHandle !== false) {
                fclose($lockHandle);
            }
            throw new RuntimeException("Proses rekonsiliasi tahun {$tahun} sedang berjalan pada proses lain.");
        }

        $tmpFile = $this->snapshotDir . sprintf('verif_0meter_%04d.json.tmp.%d', $tahun, getmypid());

        try {
            $unitCabangMap = $this->fetchUnitCabangMap();
            $verifications = $this->fetchVerifications($tahun);
            $targetsByMonth = $this->fetchTargets($tahun);

            $snapshotData = $this->reconcile($tahun, $targetsByMonth, $verifications, $unitCabangMap);
            $jsonContent = json_encode($snapshotData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

            if ($jsonContent === false) {
                throw new RuntimeException('Gagal melakukan serialisasi JSON snapshot: ' . json_last_error_msg());
            }

            if (file_put_contents($tmpFile, $jsonContent) === false) {
                throw new RuntimeException("Gagal menulis file temporary snapshot: {$tmpFile}");
            }

            $finalPath = $this->getSnapshotPath($tahun);

            if (! rename($tmpFile, $finalPath)) {
                throw new RuntimeException("Gagal mempublikasikan file snapshot secara atomik ke {$finalPath}");
            }

            return $snapshotData;
        } catch (\Throwable $e) {
            if (file_exists($tmpFile)) {
                @unlink($tmpFile);
            }
            throw $e;
        } finally {
            flock($lockHandle, LOCK_UN);
            fclose($lockHandle);
        }
    }

    public function reconcile(int $tahun, array $targetsByMonth, array $verifications, array $unitCabangMap): array
    {
        $currentYear = (int) date('Y');
        $currentMonth = (int) date('n');

        $branches = [];

        foreach ($unitCabangMap as $unit => $cabangInfo) {
            $cabangId = (string) $cabangInfo['cabang'];

            if (! isset($branches[$cabangId])) {
                $branches[$cabangId] = [
                    'id' => $cabangId,
                    'nama' => (string) $cabangInfo['nama_cabang'],
                    'target_bulan_ini' => array_fill(0, 12, 0),
                    'verifikasi_bulan_ini' => array_fill(0, 12, 0),
                    'verifikasi_kumulatif' => array_fill(0, 12, 0),
                    'realisasi_bulan_ini' => array_fill(0, 12, 0),
                    'target_awal_bulan' => array_fill(0, 12, 0),
                    'terverifikasi_sebelum_bulan_ini' => array_fill(0, 12, 0),
                    'aktivitas_verifikasi_mentah' => array_fill(0, 12, 0),
                ];
            }
        }

        $verifByCust = [];

        foreach ($verifications as $v) {
            $nosamw = (string) ($v['nosamw'] ?? '');
            $tglVerif = (string) ($v['tgl_verif'] ?? '');

            if ($nosamw === '' || $tglVerif === '') {
                continue;
            }

            $verifByCust[$nosamw][] = [
                'tgl' => $tglVerif,
                'status' => $v['status'] ?? null,
            ];

            $unit = substr($nosamw, 0, 2);
            $cabangId = $unitCabangMap[$unit]['cabang'] ?? null;
            $month = (int) date('n', strtotime($tglVerif));
            $verifYear = (int) date('Y', strtotime($tglVerif));

            if ($cabangId !== null && $verifYear === $tahun && $month >= 1 && $month <= 12 && isset($branches[$cabangId])) {
                $branches[$cabangId]['aktivitas_verifikasi_mentah'][$month - 1]++;
            }
        }

        foreach ($branches as $cabangId => &$branch) {
            $kumulatifRealisasi = 0;

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $idx = $bulan - 1;
                $targets = $targetsByMonth[$bulan] ?? [];
                $firstDayMonth = sprintf('%04d-%02d-01', $tahun, $bulan);
                $firstDayNextMonth = sprintf('%04d-%02d-01', $bulan === 12 ? $tahun + 1 : $tahun, $bulan === 12 ? 1 : $bulan + 1);

                $targetCount = 0;
                $sebelumCount = 0;
                $realisasiCount = 0;

                foreach ($targets as $noSam => $rowUnit) {
                    if (($unitCabangMap[$rowUnit]['cabang'] ?? null) !== $cabangId) {
                        continue;
                    }

                    $targetCount++;
                    $custVerifs = $verifByCust[$noSam] ?? [];

                    $verifiedSebelum = false;
                    $verifiedBulanIni = false;

                    foreach ($custVerifs as $cv) {
                        $tgl = $cv['tgl'];

                        if ($tgl < $firstDayMonth) {
                            $verifiedSebelum = true;
                        } elseif ($tgl < $firstDayNextMonth) {
                            $verifiedBulanIni = true;
                        }
                    }

                    if ($verifiedSebelum) {
                        $sebelumCount++;
                    } elseif ($verifiedBulanIni) {
                        $realisasiCount++;
                    }
                }

                $branch['target_awal_bulan'][$idx] = $targetCount;
                $branch['terverifikasi_sebelum_bulan_ini'][$idx] = $sebelumCount;
                $branch['target_bulan_ini'][$idx] = max(0, $targetCount - $sebelumCount);
                $branch['realisasi_bulan_ini'][$idx] = $realisasiCount;
                $branch['verifikasi_bulan_ini'][$idx] = $realisasiCount;

                $kumulatifRealisasi += $realisasiCount;
                $isPassedMonth = ($tahun < $currentYear) || ($tahun === $currentYear && $bulan <= $currentMonth);
                $branch['verifikasi_kumulatif'][$idx] = $isPassedMonth ? $kumulatifRealisasi : 0;
            }
        }
        unset($branch);

        return [
            'tahun' => $tahun,
            'updated_at' => date('Y-m-d H:i:s'),
            'source_status' => 'complete',
            'cabang' => array_values($branches),
        ];
    }

    public function fetchUnitCabangMap(): array
    {
        $builder = $this->dbDefault->table('munit')
            ->select('munit.unit, munit.satker AS cabang, mcabang.nm_cabang AS nama_cabang')
            ->join('mcabang', 'munit.satker = mcabang.id_cabang', 'inner');

        $map = [];

        foreach ($builder->get()->getResultArray() as $row) {
            $unit = (string) $row['unit'];
            $map[$unit] = [
                'unit' => $unit,
                'cabang' => (string) $row['cabang'],
                'nama_cabang' => (string) $row['nama_cabang'],
            ];
        }

        return $map;
    }

    public function fetchVerifications(int $tahun): array
    {
        $tglAwal = sprintf('%04d-12-01 00:00:00', $tahun - 1);
        $tglAkhir = sprintf('%04d-01-01 00:00:00', $tahun + 1);

        $builder = $this->dbGis->table('verif_0meter')
            ->select('id, nosamw, tgl_verif, status')
            ->where('tgl_verif >=', $tglAwal)
            ->where('tgl_verif <', $tglAkhir)
            ->orderBy('tgl_verif', 'ASC');

        return $builder->get()->getResultArray();
    }

    public function fetchTargets(int $tahun): array
    {
        $targetsByMonth = [];

        for ($bulan = 1; $bulan <= 12; $bulan++) {
            $targetsByMonth[$bulan] = [];

            if ($bulan === 1) {
                $tglAwal = sprintf('%04d-12-01', $tahun - 1);
                $tglAkhir = sprintf('%04d-01-01', $tahun);
            } else {
                $tglAwal = sprintf('%04d-%02d-01', $tahun, $bulan - 1);
                $tglAkhir = sprintf('%04d-%02d-01', $tahun, $bulan);
            }

            $builder = $this->dbDefault->table('baca_meter')
                ->select('no_sam')
                ->where('tgl >=', $tglAwal)
                ->where('tgl <', $tglAkhir)
                ->where('LOWER(kondisi)', 'normal')
                ->where('pakai', 0);

            foreach ($builder->get()->getResultArray() as $row) {
                $noSam = trim((string) ($row['no_sam'] ?? ''));

                if (strlen($noSam) === 7 && ctype_digit($noSam)) {
                    $targetsByMonth[$bulan][$noSam] = substr($noSam, 0, 2);
                }
            }
        }

        return $targetsByMonth;
    }
}
