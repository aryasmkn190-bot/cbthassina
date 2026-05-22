<?php

namespace App\Models;

use CodeIgniter\Model;

class SesiModel extends Model
{
    protected $table            = 'sesi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'nama', 'waktu_mulai', 'waktu_selesai', 'is_active'];
}
