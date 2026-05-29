<?php

namespace App\Models;

use CodeIgniter\Model;

class RaporNilaiModel extends Model
{
    protected $table            = 'rapor_nilai';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'peserta_id', 'mata_pelajaran_id', 'nilai', 'grade', 'semester', 'tahun_ajaran'
    ];

    public function getWithPesertaAndKelas($kelas_id = null)
    {
        $builder = $this->select('rapor_nilai.*, peserta.nama AS nama_peserta, peserta.username AS username_peserta, kelas.nama AS nama_kelas, mata_pelajaran.nama AS mata_pelajaran_nama')
            ->join('peserta', 'peserta.id = rapor_nilai.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->join('mata_pelajaran', 'mata_pelajaran.id = rapor_nilai.mata_pelajaran_id', 'left');

        if ($kelas_id) {
            $builder->where('peserta.kelas_id', $kelas_id);
        }

        return $builder->orderBy('kelas.nama', 'ASC')
            ->orderBy('peserta.nama', 'ASC')
            ->orderBy('mata_pelajaran.nama', 'ASC')
            ->findAll();
    }
}
