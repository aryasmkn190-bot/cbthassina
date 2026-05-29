<?php

namespace App\Models;

use CodeIgniter\Model;

class PrestasiModel extends Model
{
    protected $table            = 'kesiswaan_prestasi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'peserta_id', 'nama_prestasi', 'tingkat', 'kategori', 'tanggal'
    ];

    public function getWithPeserta()
    {
        return $this->select('kesiswaan_prestasi.*, peserta.nama AS peserta_nama, peserta.nisn AS peserta_nisn, kelas.nama AS kelas_nama')
            ->join('peserta', 'peserta.id = kesiswaan_prestasi.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->orderBy('kesiswaan_prestasi.tanggal', 'DESC')
            ->findAll();
    }
}
