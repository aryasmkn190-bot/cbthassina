<?php

namespace App\Models;

use CodeIgniter\Model;

class JadwalPelajaranModel extends Model
{
    protected $table            = 'jadwal_pelajaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'kelas_id', 'mata_pelajaran_id', 'hari', 'waktu_mulai', 'waktu_selesai', 'guru_nama', 'ruangan', 'guru_id'
    ];

    public function getWithKelas()
    {
        return $this->select('jadwal_pelajaran.*, kelas.nama AS nama_kelas, mata_pelajaran.nama AS mata_pelajaran_nama, users.full_name AS guru_nama_joined')
            ->join('kelas', 'kelas.id = jadwal_pelajaran.kelas_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = jadwal_pelajaran.mata_pelajaran_id', 'left')
            ->join('users', 'users.id = jadwal_pelajaran.guru_id', 'left')
            ->orderBy('kelas.nama', 'ASC')
            ->orderBy('jadwal_pelajaran.hari', 'ASC')
            ->orderBy('jadwal_pelajaran.waktu_mulai', 'ASC')
            ->findAll();
    }
}
