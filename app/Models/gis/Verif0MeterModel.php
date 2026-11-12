<?php

namespace App\Models\gis;

use CodeIgniter\Model;

class Verif0MeterModel extends Model
{
    protected $DBGroup = "gis";
    protected $table = 'verif_0meter';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
        "id",
        "nosamw",
        "nama",
        "alamat",
        "no_wm",
        "merk_wm",
        "dm_wm",
        "stant_wm",
        "latlng",
        "kondisi_wm",
        "penyebab",
        "tindak_lanjut",
        "tgl_verif",
        "keterangan",
        "user",
        "foto",
        "date",
        "status",
        "geom",
        "hasil_verif",
    ];

    public function getVerif0ByUnitMonth(string $tglAwal, string $tglAkhir): array
    {
        return $this->select("DATE_FORMAT(tgl_verif, '%Y%m') AS periode, LEFT(nosamw, 2) AS unit, COUNT(*) AS total", false)
            ->where('tgl_verif >=', $tglAwal)
            ->where('tgl_verif <', $tglAkhir)
            ->groupBy("DATE_FORMAT(tgl_verif, '%Y%m'), LEFT(nosamw, 2)", false)
            ->findAll();
    }
}
