<?php

namespace App\Models;

use CodeIgniter\Model;

class PengajuanEditSiswaModel extends Model
{
    protected $table            = 'pengajuan_edit_siswa';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'peserta_id', 'data_lama', 'data_baru', 'status', 'catatan_admin'
    ];

    public function getPendingRequest($pesertaId)
    {
        return $this->where('peserta_id', $pesertaId)
            ->where('status', 'menunggu')
            ->orderBy('created_at', 'DESC')
            ->first();
    }

    public function getWithRelations()
    {
        return $this->select('pengajuan_edit_siswa.*, peserta.nama AS peserta_nama, peserta.nisn AS peserta_nisn, kelas.nama AS kelas_nama')
            ->join('peserta', 'peserta.id = pengajuan_edit_siswa.peserta_id', 'inner')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->orderBy('pengajuan_edit_siswa.created_at', 'DESC')
            ->findAll();
    }
}
