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

        return $builder->findAll();
    }

    public function getDataGagalPerUser($tglAwal, $tglAkhir, $user)
    {
        return $this
            ->select("no_sam AS nosamw")
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->where("user", $user)
            ->where("cek", 3)
            ->findAll();
    }

    /**
     * @param string $tglAwal
     * @param string $tglAkhir
     * @param string $cek
     * @param string $order
     * @param int|null $page
     * @param int|null $size
     * @param string|null $sort
     * @param string|null $cabang
     * @param string|null $petugas
     * @param string|null $kampung
     * @param string|null $kondisi
     * @param string|null $nosamw
     * @return array<object>
     */
    public function getDataVerif(
        string $tglAwal,
        string $tglAkhir,
        string $cek,
        string $order,
        ?int $page = null,
        ?int $size = null,
        ?string $sort = null,
        ?string $cabang = null,
        ?string $petugas = null,
        ?string $kampung = null,
        ?string $kondisi = null,
        ?string $nosamw = null
    ): array {
        $offset = $page > 0 ? ($page - 1) * $size : 0;

        $builder = $this->select("
            baca_meter.tgl AS tgl,
            baca_meter.no_sam AS no_sam,
            baca_meter.stan_kini AS stan_kini,
            baca_meter.stan_lalu AS stan_lalu,
            baca_meter.pakai AS pakai,
            baca_meter.petugas AS petugas,
            baca_meter.kondisi AS kondisi,
            baca_meter.ket AS ket,
            baca_meter.info AS info,
            baca_meter.ptgs_met AS ptgs_met,
            baca_meter.rata AS rata,
            LEFT ( baca_meter.no_sam, 2 ) AS kd_wil,
            munit.nama AS wil,
            munit.satker AS kd_cabang,
            mcabang.nm_cabang AS nm_cabang,
            t_status_cek.kd_cek AS kd_cek,
            t_status_cek.status AS status_cek,
            baca_meter.nama AS nama,
            baca_meter.alamat AS alamat 
        ")
            ->join("munit", "SUBSTRING(baca_meter.no_sam,1,2)=munit.unit")
            ->join("mcabang", "munit.satker=mcabang.id_cabang")
            ->join("t_status_cek", "baca_meter.cek=t_status_cek.kd_cek")
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'");
        if ($size && $size > 0) {
            $builder->limit($size, $offset);
        }
        if ($cek == "0") {
            $builder->whereIn("cek", ["0", ""]);
        } elseif ($cek == "4") {
            $builder->whereIn("cek", ["0", "1", "2", "3",""]);
        } else {
            $builder->where("cek", $cek);
        }

        if ($nosamw) {
            $builder->where("no_sam", $nosamw);
        }

        if ($cabang) {
            $builder->where('munit.satker', $cabang);
        }

        if ($petugas) {
            $builder->where("user", $petugas);
        }

        if ($kampung) {
            $builder->where("ptgs_met", $kampung);
        }

        if ($kondisi) {
            $builder->where("kondisi", $kondisi);
        }

        if ($sort) {
            $builder->orderBy($sort, $order);
        }

        $result = $builder->findAll();
        // echo $this->getLastQuery();
        return $result;
    }

    public function getTotalData(
        $tglAwal,
        $tglAkhir,
        $cek,
        $cabang = null,
        $petugas = null,
        $kampung = null,
        $kondisi = null,
        $nosamw = null
    ) {
        $builder = $this->select("count(*) AS total")
            ->where("tgl BETWEEN '$tglAwal' AND '$tglAkhir'");

        if ($cek == "0")
            $builder->whereIn("cek", ["0", ""]);
        elseif ($cek == "4")
            $builder->whereIn("cek", ["0", "1", "2", "3",""]);
        else
            $builder->where("cek", $cek);

        if ($nosamw)
            $builder->where("no_sam", $nosamw);

        if ($cabang)
            $builder->join("pegawai", "ptgs_met=nik")
                ->where("wil", $cabang);

        if ($petugas)
            $builder->where("user", $petugas);

        if ($kampung)
            $builder->where("ptgs_met", $kampung);

        if ($kondisi)
            $builder->where("kondisi", $kondisi);
        $result= $builder->first();
        // echo $this->getLastQuery();
        return $result;
    }

    public function cekFoto($nosamw, $tglAwal, $tglAkhir)
    {
        return $this->select("
                baca_meter.no AS id, 
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
            ->where("no_sam", $nosamw)
            ->orderBy("tgl", "asc")
            ->findAll();
    }

    public function getHasilBaca0(string $tglAwal, string $tglAkhir,  ?string $satker = null,  ?int $size = null, ?int $offset = null): array
    {

        $builder = $this->select("
                baca_meter.tgl AS tgl,
                baca_meter.no_sam AS no_sam,
                baca_meter.stan_kini AS stan_kini,
                baca_meter.stan_lalu AS stan_lalu,
                baca_meter.pakai AS pakai,
                baca_meter.petugas AS petugas,
                baca_meter.kondisi AS kondisi,
                baca_meter.ket AS ket,
                baca_meter.info AS info,
                baca_meter.ptgs_met AS ptgs_met,
                baca_meter.rata AS rata,
                LEFT ( baca_meter.no_sam, 2 ) AS kd_wil,
                munit.nama AS wil,
                munit.satker AS kd_cabang,
                mcabang.nm_cabang AS nm_cabang,
                t_status_cek.kd_cek AS kd_cek,
                t_status_cek.status AS status_cek,
                baca_meter.nama AS nama,
                baca_meter.alamat AS alamat 
            ")
            ->join("munit", "SUBSTRING(baca_meter.no_sam,1,2)=munit.unit")
            ->join("mcabang", "munit.satker=mcabang.id_cabang")
            ->join("t_status_cek", "baca_meter.cek=t_status_cek.kd_cek")
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->where("baca_meter.pakai", 0);

        if ($satker)
            $builder->where("munit.satker", $satker);

        if ($size)
            $builder->limit($size, $offset);

        $result = $builder->findAll();
        // echo $this->getLastQuery();
        return $result;
    }

    public function getKondisiBaca(string $tglAwal, string $tglAkhir): array
    {
        $builder = $this->select("
                baca_meter.kondisi AS kondisi,
                COUNT(baca_meter.no_sam) AS total,
                munit.satker AS satker
            ")
            ->join('munit', 'SUBSTRING(baca_meter.no_sam,1,2) = munit.unit', 'inner')
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->groupBy('baca_meter.kondisi')
            ->groupBy("munit.satker");
        return $builder->findAll();
    }

    /**
     * @param string $tglAwal
     * @param string $tglAkhir
     * @return array<array{kondisi:string, total:int, satker:string}>
     */
    public function getKondisiBaca0(string $tglAwal, string $tglAkhir): array
    {
        $builder = $this->select("
                baca_meter.kondisi AS kondisi,
                COUNT(baca_meter.no_sam) AS total,
                munit.satker AS satker
            ")
            ->join('munit', 'SUBSTRING(baca_meter.no_sam,1,2) = munit.unit', 'inner')
            ->where("baca_meter.tgl BETWEEN '$tglAwal' AND '$tglAkhir'")
            ->where("baca_meter.pakai", 0)
            ->groupBy('baca_meter.kondisi')
            ->groupBy("munit.satker");
        return $builder->findAll();
    }
}
