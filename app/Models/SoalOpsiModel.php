<?php

namespace App\Models;

use CodeIgniter\Model;

class SoalOpsiModel extends Model
{
    protected $table            = 'soal_opsi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;

    protected $allowedFields    = [
        'id',
        'soal_id',
        'label',
        'teks',
        'pasangan',   // untuk jodohkan
        'is_true',     // untuk jawaban benar
        'bobot',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
