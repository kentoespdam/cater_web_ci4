<?php

namespace App\Libraries;

use App\Models\gis\Verif0MeterModel;
use App\Services\BacaMeterQueryService;

class Verif0MeterGrafikService
{
    public function __construct(
        private readonly ?BacaMeterQueryService $bacaMeterQueryService = null,
    ) {
    }

    public function getBulanan(int $tahun): array
    {
        $queryService = $this->bacaMeterQueryService ?? new BacaMeterQueryService();
        $tglAwalVerif = sprintf('%04d-01-01', $tahun);
        $tglAkhirVerif = sprintf('%04d-01-01', $tahun + 1);
        $currentMonth = date('m');
        $unitCabang = [];
        $branches = [];

        foreach ($queryService->getUnitCabangMap() as $row) {
            $unitCabang[(string) $row->unit] = (string) $row->cabang;
            $branches[(string) $row->cabang] = [
                'id' => (string) $row->cabang,
                'nama' => (string) $row->nama_cabang,
            ];
        }

        $verifikasiByBranchMonth = [];

        foreach ((new Verif0MeterModel())->getVerif0ByUnitMonth($tglAwalVerif, $tglAkhirVerif) as $row) {
            $cabang = $unitCabang[(string) $row->unit] ?? null;

            if ($cabang === null) {
                continue;
            }
            $key = (string) $row->periode;
            $verifikasiByBranchMonth[$cabang][$key] = ($verifikasiByBranchMonth[$cabang][$key] ?? 0) + (int) $row->total;
        }

        // print_r($verifikasiByBranchMonth); // Debugging line to check the structure of the array

        $bacaMeterRows = $queryService
            ->getNormalPakai0ByMonth(sprintf('%04d-12-01', $tahun - 1), sprintf('%04d-12-01', $tahun));
        $bacaMeterByBranchMonth = [];

        foreach ($bacaMeterRows as $row) {
            $cabang = (string) $row->cabang;
            $branches[$cabang] = ['id' => $cabang, 'nama' => (string) $row->nama_cabang];
            $key = (string) $row->periode;
            $bacaMeterByBranchMonth[$cabang][$key] = (int) $row->total;
        }

        // print_r($bacaMeterByBranchMonth); // Debugging line to check the structure of the array

        foreach ($branches as $cabang => &$branch) {
            $branch['verifikasi_kumulatif'] = [];
            $branch['verifikasi_bulan_ini'] = [];
            $branch['target_bulan_ini'] = [];
            $verifikasiKumulatif = 0;
            $verifikasiPerBulan = [];

            for ($bulan = 1; $bulan <= 12; $bulan++) {
                $keyBulanIni = sprintf('%04d%02d', $tahun, $bulan);
                $verifikasiPerBulan[$bulan] = $verifikasiByBranchMonth[$cabang][$keyBulanIni] ?? 0;
                $verifikasiKumulatif += $verifikasiPerBulan[$bulan];
                $branch['verifikasi_kumulatif'][] = $bulan <= $currentMonth ? $verifikasiKumulatif : 0;
                $branch['verifikasi_bulan_ini'][] = $verifikasiPerBulan[$bulan];

                $bacaMeter = $bacaMeterByBranchMonth[$cabang][$keyBulanIni] ?? 0;
                $verifikasiKumulatifSebelumBulanIni = $verifikasiKumulatif - $verifikasiPerBulan[$bulan];
                $branch['target_bulan_ini'][] = max(0, $bacaMeter - $verifikasiKumulatifSebelumBulanIni);
            }
        }
        unset($branch);

        return [
            'tahun' => $tahun,
            'cabang' => array_values($branches),
        ];
    }
}
