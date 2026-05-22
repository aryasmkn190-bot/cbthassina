<?php

namespace App\Models;

use CodeIgniter\Model;

class TopikSoalModel extends Model
{
    protected $table            = 'topik_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id',
        'bank_soal_id', // tambahkan ini
        'nama',
        'keterangan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
