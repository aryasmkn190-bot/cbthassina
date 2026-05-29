<?php

namespace App\Models;

use CodeIgniter\Model;

class KeuanganSppModel extends Model
{
    protected $table            = 'keuangan_spp';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'peserta_id', 'bulan', 'nominal', 'status_bayar', 'metode_bayar', 'tanggal_bayar', 'invoice_number'
    ];

    public function getWithPesertaAndKelas($kelas_id = null)
    {
        $builder = $this->select('keuangan_spp.*, peserta.nama AS nama_peserta, peserta.username AS username_peserta, kelas.nama AS nama_kelas')
            ->join('peserta', 'peserta.id = keuangan_spp.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left');

        if ($kelas_id) {
            $builder->where('peserta.kelas_id', $kelas_id);
        }

        return $builder->orderBy('keuangan_spp.created_at', 'DESC')
            ->findAll();
    }
}
