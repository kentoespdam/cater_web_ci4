<?php

namespace App\Models\Sikompak;

use CodeIgniter\Model;

class CabangModel extends Model
{
    protected $DBGroup          = "sikompak";
    protected $table            = 'mcabang';
    protected $primaryKey       = 'id_cabang';
    protected $useAutoIncrement = false;
    protected $returnType       = 'object';
    protected $allowedFields    = ['id_cabang', 'nm_cabang'];
}
