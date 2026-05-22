<?php

namespace App\Models;

use CodeIgniter\Model;

class JenisUjianModel extends Model
{
    protected $table            = 'jenis_ujian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'id',
        'nama',
        'kode',
        'deskripsi',
        'is_active',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
