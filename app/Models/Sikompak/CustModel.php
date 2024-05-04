<?php

namespace App\Models\Sikompak;

use CodeIgniter\Model;
use \Config\Database\connect;

class CustModel extends Model
{
    protected $DBGroup = "sikompak";
    protected $table = 'cust';
    protected $primaryKey = 'noreg';
    protected $useAutoIncrement = false;
    protected $returnType = 'object';
    protected $allowedFields = [
        'noreg',
        'nopm',
        'nosamw',
        'jlw',
        'nama',
        'alamat',
        'rt',
        'rw',
        'kodepos',
        'desa',
        'kecamatan',
        'job_plg',
        'jml_org',
        'tgl_daf',
        'tgl_pas',
        'tgl_stat',
        'tgl_diakui',
        'ujl',
        'dnmet',
        'no_met',
        'tgl_met',
        'dia_met',
        'stat_smb',
        'tg',
        'opr',
        'loket',
        'ptgs_met',
        'ptgs_gbr',
        'piu_sb',
        'jang_sb',
        'nang_sb',
        'telp',
        'ktp',
        'pemasang',
        'no_gbr',
        'fl_gbr',
        'no_byrsmb',
        'tgl_byr',
        'tgl_ref',
        'no_ref',
        'no_rab',
        'tgl_rab',
        'no_bppi',
        'tgl_bppi',
        'no_spk',
        'tgl_spk',
        'loketkol',
        'bsmbr',
        'bujl',
        'bpipa',
        'bppn',
        'pang_sb',
        'ketmet',
        'urjlw',
        'urjlwp',
        'urstat_smb',
        'tgl_reg',
        'no_reff',
        'merk_met',
        'blain',
        'no_reg',
        'bregist',
        'namaold',
        'tgl_sgl',
        'tgl_lkt',
        'tgl_cbt',
        'tgb_sgl',
        'tgb_lkt',
        'tgb_cbt',
        'nosamw_lm',
        'ket_edit',
        'user_pembaca',
        'longi',
        'lati',
        'kode_sumber',
        'kode_wil',
        'unit'
    ];

    public function getTotalPelangganPerPembaca()
    {
        $db = \Config\Database::connect("sikompak");
        $builder = $db->table("$this->table c")
            ->select("
                COUNT(c.noreg) AS jml_pelanggan, 
                p.pembaca_meter AS petugas,
                p.nama_lengkap AS nama,
                p.cabang
            ")
            ->join("pegawai p", "c.ptgs_met = p.NIK")
            ->where("c.stat_smb", "30")
            ->groupBy("p.pembaca_meter")
            ->get();
        return $builder->getResultObject();
    }

    public function getDetailKampungPelanggan($kdPelanggan)
    {
        if (count($kdPelanggan) == 0) return [];
        $db = \Config\Database::connect("sikompak");
        $builder = $db->table("$this->table c")
            ->select("
            c.nosamw, 
            c.nama, 
            p.Nama as kampung
        ")
            ->join("pegawai p", "c.ptgs_met = p.NIK")
            ->whereIn("c.nosamw", $kdPelanggan)
            ->get();

        return $builder->getResultObject();
    }
}
