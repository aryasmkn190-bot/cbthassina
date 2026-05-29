<?php

namespace App\Models;

use CodeIgniter\Model;

class PelanggaranModel extends Model
{
    protected $table            = 'kesiswaan_pelanggaran';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'peserta_id', 'nama_pelanggaran', 'kategori', 'point', 'tanggal', 'tindakan'
    ];

    public function getWithPeserta()
    {
        return $this->select('kesiswaan_pelanggaran.*, peserta.nama AS peserta_nama, peserta.nisn AS peserta_nisn, kelas.nama AS kelas_nama')
            ->join('peserta', 'peserta.id = kesiswaan_pelanggaran.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->orderBy('kesiswaan_pelanggaran.tanggal', 'DESC')
            ->findAll();
    }
}
