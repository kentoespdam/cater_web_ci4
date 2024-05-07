<?php

namespace App\Models;

use CodeIgniter\Model;

class KondisiModel extends Model
{
    protected $table            = 't_kondisi_wm';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $allowedFields    = ["id", "kd_kondisi", "kondisi"];
}
