<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasModel extends Model
{
    protected $table            = 'tugas';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'kelas_id', 'judul', 'deskripsi', 'tenggat_waktu', 'mata_pelajaran_id'
    ];

    public function getWithKelas()
    {
        return $this->select('tugas.*, kelas.nama AS nama_kelas, mata_pelajaran.nama AS mata_pelajaran_nama')
            ->join('kelas', 'kelas.id = tugas.kelas_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = tugas.mata_pelajaran_id', 'left')
            ->orderBy('tugas.created_at', 'DESC')
            ->findAll();
    }
}
