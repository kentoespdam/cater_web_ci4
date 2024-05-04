<?php

namespace App\Models;

use CodeIgniter\Model;

class AuthModel extends Model
{
    protected $DBGroup = "default";
    protected $table = 'user_web';
    protected $primaryKey = 'uid';
    protected $useAutoIncrement = true;
    protected $returnType = 'object';
    protected $allowedFields = [
        "uid",
        "username",
        "nama_lengkap",
        "cabang",
        "encrypted_password",
        "kd_status"
    ];

}
