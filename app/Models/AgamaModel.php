<?php

namespace App\Models;

use CodeIgniter\Model;

class AgamaModel extends Model
{
    protected $table            = 'agama';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'nama', 'is_active'];
}
