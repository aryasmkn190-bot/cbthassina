<?php

namespace App\Models;

use CodeIgniter\Model;

class BeritaAcaraModel extends Model
{
    protected $table            = 'berita_acara';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false; // karena id UUID manual
    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;
    // timestamps
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $allowedFields = [
        'id',
        'jenis_ujian_id',
        'ujian_id',
        'ruang_id',
        'sesi_id',
        'tanggal',
        'jam_mulai',
        'jam_selesai',
        'jumlah_peserta_seharusnya',
        'jumlah_hadir',
        'jumlah_tidak_hadir',
        'peserta_tidak_hadir',
        'catatan',
        'proktor_nama',
        'proktor_nip',
        'pengawas_nama',
        'pengawas_nip',
        'kepala_sekolah_nama',
        'kepala_sekolah_nip',
        'created_at',
        'updated_at',
    ];


    public function getWithJoin($id = null)
    {
        $builder = $this->select(
            'berita_acara.*, 
         ujian.nama_ujian,
         jenis_ujian.nama AS nama_jenis_ujian,
         ruang.nama AS nama_ruang,
         sesi.nama AS nama_sesi'
        )
            ->join('ujian', 'ujian.id = berita_acara.ujian_id', 'left')
            ->join('jenis_ujian', 'jenis_ujian.id = ujian.jenis_ujian_id', 'left')
            ->join('ruang', 'ruang.id = berita_acara.ruang_id', 'left')
            ->join('sesi', 'sesi.id = berita_acara.sesi_id', 'left')
            ->orderBy('berita_acara.created_at', 'DESC');

        if ($id) {
            return $builder->where('berita_acara.id', $id)->first();
        }

        return $builder->findAll();
    }
}
