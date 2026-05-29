<?php

namespace App\Models;

use CodeIgniter\Model;

class TugasJawabanModel extends Model
{
    protected $table            = 'tugas_jawaban';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'tugas_id', 'peserta_id', 'file_path', 'catatan_guru', 'nilai', 'tanggal_kirim'
    ];

    public function getSubmissions($tugas_id)
    {
        return $this->select('tugas_jawaban.*, peserta.nama AS nama_peserta, peserta.username AS username_peserta')
            ->join('peserta', 'peserta.id = tugas_jawaban.peserta_id', 'left')
            ->where('tugas_jawaban.tugas_id', $tugas_id)
            ->orderBy('peserta.nama', 'ASC')
            ->findAll();
    }
}
