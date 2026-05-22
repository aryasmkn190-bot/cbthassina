<?php

namespace App\Models;

use CodeIgniter\Model;

class KelasModel extends Model
{
    protected $table            = 'kelas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'nama', 'is_active'];

    public function getActiveSorted()
    {
        return $this->where('is_active', 1)
            ->orderBy('nama', 'ASC')
            ->findAll();
    }
    public function getSorted()
    {
        return $this->orderBy('nama', 'ASC')
            ->findAll();
    }
}
