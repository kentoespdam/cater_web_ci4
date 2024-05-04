<?php

namespace App\Models\Sikompak;

use CodeIgniter\Model;

class PegawaiModel extends Model
{
    protected $table            = 'pegawai';
    protected $primaryKey       = 'nik';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $allowedFields    = [
        "NIK",
        "Nama",
        "Nama_Lengkap",
        "Jabatan",
        "ID",
        "pembaca_meter",
        "WIL",
        "cabang",
        "jmltarget",
        "jmlterbaca",
        "persentase"
    ];
}
