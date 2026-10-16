<?php

namespace App\Services;

use Config\Database;

class SelisihFotoService
{
    protected string $basePath;
    protected $db;

    public function __construct(?string $basePath = null, $db = null)
    {
        $this->basePath = rtrim($basePath ?? (config('App')->fotoWmPath ?? ROOTPATH . 'fotowm'), '/');
        $this->db = $db ?? Database::connect();
    }

    public function listFile(string $petugas, string $bulan, string $tahun): array
    {
        $normBulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $path = $this->basePath . '/' . $tahun . $normBulan . '/' . $petugas;

        if (!is_dir($path)) {
            return [];
        }

        $files = scandir($path, SCANDIR_SORT_NONE);

        if ($files === false) {
            return [];
        }

        $result = [];

        foreach ($files as $file) {
            if ($file !== '.' && $file !== '..') {
                $result[] = $file;
            }
        }

        return $result;
    }

    public function findByCabang(string $cabang, string $bulan, string $tahun): array
    {
        $normBulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $startDate = "{$tahun}-{$normBulan}-01";

        $builder = $this->db->table('baca_meter bm');
        $builder->select("
            p.cabang,
            bm.user AS petugas,
            bm.no_sam AS nosamw,
            bm.nama AS nama,
            bm.alamat AS alamat,
            bm.ptgs_met AS kode_kampung,
            p.Nama AS kampung,
            bm.file AS foto
        ");
        $builder->join('pegawai p', 'bm.ptgs_met = p.NIK', 'left');
        $builder->where('p.WIL', $cabang);
        $builder->where('bm.tgl >=', $startDate);
        $builder->where('bm.tgl <=', "LAST_DAY('{$startDate}')", false);

        $query = $builder->get();
        $results = $query->getResultObject();

        $grouped = [];

        foreach ($results as $row) {
            $petugas = $row->petugas ?? 'unknown';

            if (!isset($grouped[$petugas])) {
                $grouped[$petugas] = [];
            }
            $grouped[$petugas][] = $row;
        }

        $allRows = [];
        $totalData = 0;
        $totalFoto = 0;

        foreach ($grouped as $petugas => $rows) {
            $files = $this->listFile($petugas, $normBulan, $tahun);
            $fileLookup = array_flip($files);

            foreach ($rows as $row) {
                $totalData++;
                $fotoName = $row->foto ?? '';
                $hasFoto = isset($fileLookup[$fotoName]) ? 1 : 0;
                $totalFoto += $hasFoto;

                if (!$hasFoto) {
                    $row->selisih_status = 'MISSING_PHOTO';
                    $allRows[] = $row;
                }
            }
        }

        $selisihCount = $totalData - $totalFoto;

        return [
            'jmlData' => $totalData,
            'jmlFoto' => $totalFoto,
            'selisih' => $selisihCount,
            'data'    => $allRows,
        ];
    }

    public function findByPegawai(string $petugas, string $bulan, string $tahun): array
    {
        $normBulan = str_pad($bulan, 2, '0', STR_PAD_LEFT);
        $startDate = "{$tahun}-{$normBulan}-01";

        $builder = $this->db->table('baca_meter bm');
        $builder->select("
            p.cabang,
            bm.user AS petugas,
            bm.no_sam AS nosamw,
            bm.nama AS nama,
            bm.alamat AS alamat,
            bm.ptgs_met AS kode_kampung,
            p.Nama AS kampung,
            bm.file AS foto
        ");
        $builder->join('pegawai p', 'bm.ptgs_met = p.NIK', 'left');
        $builder->where('bm.user', $petugas);
        $builder->where('bm.tgl >=', $startDate);
        $builder->where('bm.tgl <=', "LAST_DAY('{$startDate}')", false);

        $query = $builder->get();
        $results = $query->getResultObject();

        $files = $this->listFile($petugas, $normBulan, $tahun);
        $fileLookup = array_flip($files);

        $allRows = [];
        $totalData = 0;
        $totalFoto = 0;

        foreach ($results as $row) {
            $totalData++;
            $fotoName = $row->foto ?? '';
            $hasFoto = isset($fileLookup[$fotoName]) ? 1 : 0;
            $totalFoto += $hasFoto;

            if (!$hasFoto) {
                $row->selisih_status = 'MISSING_PHOTO';
                $allRows[] = $row;
            }
        }

        $selisihCount = $totalData - $totalFoto;

        return [
            'jmlData' => $totalData,
            'jmlFoto' => $totalFoto,
            'selisih' => $selisihCount,
            'data'    => $allRows,
        ];
    }
}
