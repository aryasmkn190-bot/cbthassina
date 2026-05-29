<?php

namespace App\Models;

use CodeIgniter\Model;

class MataPelajaranModel extends Model
{
    protected $table            = 'mata_pelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $returnType       = 'array';
    protected $allowedFields    = ['id', 'kode', 'nama', 'is_active'];

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
