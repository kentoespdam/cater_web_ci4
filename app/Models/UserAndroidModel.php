<?php

namespace App\Models;

use CodeIgniter\Model;

class UserAndroidModel extends Model
{
    protected $table            = 'users';
    protected $primaryKey       = 'uid';
    protected $useAutoIncrement = true;
    protected $returnType       = 'object';
    protected $useSoftDeletes   = false;
    protected $protectFields    = true;
    protected $allowedFields    = [
        "uid",
        "unique_id",
        "username",
        "nama_lengkap",
        "cabang",
        "email",
        "encrypted_password",
        "salt",
        "created_at",
        "updated_at",
        "password"
    ];

}
