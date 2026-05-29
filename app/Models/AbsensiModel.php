<?php

namespace App\Models;

use CodeIgniter\Model;

class AbsensiModel extends Model
{
    protected $table            = 'absensi';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'peserta_id', 'tanggal', 'waktu_scan', 'status'
    ];

    public function getWithPesertaAndKelas($kelas_id = null, $tanggal = null)
    {
        $builder = $this->select('absensi.*, peserta.nama AS nama_peserta, peserta.username AS username_peserta, kelas.nama AS nama_kelas')
            ->join('peserta', 'peserta.id = absensi.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left');

        if ($kelas_id) {
            $builder->where('peserta.kelas_id', $kelas_id);
        }
        if ($tanggal) {
            $builder->where('absensi.tanggal', $tanggal);
        }

        return $builder->orderBy('absensi.tanggal', 'DESC')
            ->orderBy('absensi.waktu_scan', 'DESC')
            ->findAll();
    }
}
