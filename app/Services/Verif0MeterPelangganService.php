<?php

declare(strict_types=1);

namespace App\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;

final class Verif0MeterPelangganService
{
    private readonly BaseConnection $dbDefault;
    private readonly BaseConnection $dbGis;

    public function __construct(?BaseConnection $dbDefault = null, ?BaseConnection $dbGis = null)
    {
        $this->dbDefault = $dbDefault ?? Database::connect('default');
        $this->dbGis = $dbGis ?? Database::connect('gis');
    }

    public function list(int $tahun, int $bulan, string $jenis, ?string $satker, ?string $statusManual, ?string $statusId, int $page, int $rows): array
    {
        $start = sprintf('%04d-%02d-01', $tahun, $bulan);
        $end = date('Y-m-d', strtotime($start . ' +1 month'));
        $unitMap = (new Verif0MeterReconciliationService($this->dbDefault, $this->dbGis))->fetchUnitCabangMap();

        if ($jenis === 'verifikasi') {
            $activity = $this->dbGis->table('verif_0meter')->select('nosamw, tgl_verif, nama, alamat')->where('tgl_verif >=', $start)->where('tgl_verif <', $end)->orderBy('tgl_verif', 'ASC')->get()->getResultArray();
            $rowsOut = [];

            foreach ($activity as $row) {
                $id = (string) $row['nosamw'];
                $unit = substr($id, 0, 2);
                $cabang = $unitMap[$unit]['cabang'] ?? null;

                if ($satker !== null && $satker !== '' && $cabang !== $satker) {
                    continue;
                }
                $rowsOut[] = $row + ['periode' => sprintf('%04d%02d', $tahun, $bulan), 'unit' => $unit, 'satker' => $cabang];
            }
        } else {
            $targets = $this->dbDefault->table('baca_meter')->select('no_sam, nama, alamat')->where('tgl >=', date('Y-m-d', strtotime($start . ' -1 month')))->where('tgl <', $start)->where('LOWER(kondisi)', 'normal')->where('pakai', 0)->orderBy('no_sam', 'ASC')->get()->getResultArray();
            $rowsOut = [];
            $verifiedBefore = [];
            $prior = $this->dbGis->table('verif_0meter')->select('nosamw, tgl_verif')->where('tgl_verif >=', sprintf('%04d-01-01', $tahun))->where('tgl_verif <', $start)->get()->getResultArray();
            $verifiedId = [];

            foreach ($prior as $v) {
                $id = (string) $v['nosamw'];
                $verifiedId[$id] = true;
                $verifiedBefore[$id] = true;
            }

            foreach ($targets as $row) {
                $id = trim((string) $row['no_sam']);

                if (strlen($id) !== 7 || ! ctype_digit($id)) {
                    continue;
                }
                $unit = substr($id, 0, 2);
                $cabang = $unitMap[$unit]['cabang'] ?? null;
                $manual = isset($verifiedBefore[$id]) ? 'Sudah Diverifikasi' : 'Belum Diverifikasi';
                $statusIdValue = isset($verifiedId[$id]) ? 'Sudah Diverifikasi' : 'Belum Diverifikasi';

                if (($satker !== null && $satker !== '' && $cabang !== $satker) || ($statusManual !== null && $statusManual !== '' && $manual !== $statusManual) || ($statusId !== null && $statusId !== '' && $statusIdValue !== $statusId)) {
                    continue;
                }
                $rowsOut[$id] = $row + ['no_sam' => $id, 'periode' => sprintf('%04d%02d', $tahun, $bulan), 'unit' => $unit, 'satker' => $cabang, 'status_verifikasi_manual' => $manual, 'status_verifikasi_id' => $statusIdValue];
            }
            $rowsOut = array_values($rowsOut);
        }

        $total = count($rowsOut);

        return ['jenis' => $jenis, 'tahun' => $tahun, 'bulan' => $bulan, 'page' => $page, 'rowsPerPage' => $rows, 'total' => $total, 'rows' => array_slice($rowsOut, ($page - 1) * $rows, $rows)];
    }
}
