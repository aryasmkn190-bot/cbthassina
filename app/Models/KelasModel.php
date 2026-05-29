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
    protected $allowedFields    = ['id', 'nama', 'is_active', 'wali_kelas_id'];

    public function getActiveSorted()
    {
        return $this->select('kelas.*, users.full_name AS wali_kelas_nama')
            ->join('users', 'users.id = kelas.wali_kelas_id', 'left')
            ->where('kelas.is_active', 1)
            ->orderBy('kelas.nama', 'ASC')
            ->findAll();
    }
    public function getSorted()
    {
        return $this->select('kelas.*, users.full_name AS wali_kelas_nama')
            ->join('users', 'users.id = kelas.wali_kelas_id', 'left')
            ->orderBy('kelas.nama', 'ASC')
            ->findAll();
    }
}
