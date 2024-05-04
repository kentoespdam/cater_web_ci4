<?php

namespace App\Models;

use CodeIgniter\Model;

class BacaMeterModel extends Model
{
    protected $table = 'baca_meter';
    protected $primaryKey = 'no';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';

    public function getJumlahCekData($tglAwal, $tglAkhir)
    {
        // $db = \Config\Database::connect();
        $builder = $this->select("
                petugas,
                COUNT(no_sam) AS jml_baca,
                SUM( CASE WHEN cek IN ( 1, 2, 3 ) THEN 1 ELSE 0 END ) AS cek_koperasi, 
                SUM( CASE WHEN cek =  1 THEN 1 ELSE 0 END ) AS cek_cabang, 
                SUM( CASE WHEN cek =  3 OR NULL THEN 1 ELSE 0 END ) AS gagal 
            ")
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->groupBy("petugas")
            ->orderBy("petugas");
        // $builder = $query->get();
        // echo $query->getCompiledSelect();

        return $builder->findAll();
        // return [];
    }

    public function getDataGagalPerUser($tglAwal, $tglAkhir, $user)
    {
        // $db = \Config\Database::connect();
        $builder = $this
            ->select("no_sam AS nosamw")
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->where("user", $user)
            ->where("cek", 3)
            ->get();

        return $builder->findAll();
    }

    public function getDataVerif(
        $page,
        $size,
        $tglAwal,
        $tglAkhir,
        $cek,
        $order,
        $sort = null,
        $cabang = null,
        $petugas = null,
        $kampung = null,
        $nosamw = null
    ) {
        $offset = $page > 0 ? ($page - 1) * $size : 0;
        // $db = \Config\Database::connect();
        $builder = $this->select("
                baca_meter.`no` AS id, 
                baca_meter.no_sam AS nosamw, 
                baca_meter.nama, 
                baca_meter.tgl, 
                baca_meter.info AS tgl_upload, 
                baca_meter.stan_kini, 
                baca_meter.stan_lalu, 
                baca_meter.pakai, 
                baca_meter.kondisi, 
                baca_meter.ket, 
                baca_meter.cek,
                baca_meter.petugas
            ")
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->limit($size, $offset);
        if ($cek == "0") {
            $builder->whereIn("cek", ["0", ""]);
        } else {
            $builder->where("cek", $cek);
        }

        if ($nosamw) {
            $builder->where("no_sam", $nosamw);
        }
        if ($cabang) {
            $builder->join("pegawai", "ptgs_met=nik")
                ->where("wil", $cabang);
        }
        if ($petugas) {
            $builder->where("user", $petugas);
        }
        if ($kampung) {
            $builder->where("ptgs_met", $kampung);
        }
        if ($sort) {
            $builder->orderBy($sort, $order);
        }
        return $builder->findAll();
    }

    public function getTotalData(
        $tglAwal,
        $tglAkhir,
        $cek,
        $cabang = null,
        $petugas = null,
        $kampung = null,
        $nosamw = null
    ) {
        // $db = \Config\Database::connect();
        $builder = $this->select("count(*) AS total")
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'");
        if ($cek == "0") {
            $builder->whereIn("cek", ["0", ""]);
        } else {
            $builder->where("cek", $cek);
        }
        if ($nosamw) {
            $builder->where("no_sam", $nosamw);
        }
        if ($cabang) {
            $builder->join("pegawai", "ptgs_met=nik")
                ->where("wil", $cabang);
        }
        if ($petugas) {
            $builder->where("user", $petugas);
        }
        if ($kampung) {
            $builder->where("ptgs_met", $kampung);
        }
        return $builder->first();
    }
}
