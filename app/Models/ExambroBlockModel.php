<?php

namespace App\Models;

use CodeIgniter\Model;

class ExambroBlockModel extends Model
{
    protected $table      = 'exambro_block';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'package_name',
        'app_name',
        'category',
        'is_blocked',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
}
