<?php

namespace App\Models;

use CodeIgniter\Model;

class UjianModel extends Model
{
    protected $table            = 'ujian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields = [
        'id',
        'bank_soal_id',
        'jenis_ujian_id',
        'nama_ujian',
        'deskripsi',
        'kode_ujian',
        'token',
        'acak_soal',
        'acak_opsi',
        'pakai_token',
        'durasi_ujian',
        'minimal_durasi',
        'tampil_nilai',
        'tampil_pembahasan',
        'pakai_webcam',
        'perangkat_terkunci',
        'single_login',
        'created_by',
        'waktu_mulai',
        'waktu_selesai',
        'is_active',
        'butuh_login',
        'dibagikan',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';


    /* ===============================
       GET ALL UJIAN (ADMIN)
    =============================== */
    public function getAllUjian($userId = null)
    {
        $builder = $this->select('
                ujian.*,
                bank_soal.nama AS nama_bank_soal,
                jenis_ujian.nama AS nama_jenis_ujian,
                COUNT(DISTINCT hasil_ujian.id) AS jumlah_peserta,
                COUNT(DISTINCT soal.id) AS jumlah_soal
            ')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->join('jenis_ujian', 'jenis_ujian.id = ujian.jenis_ujian_id', 'left')
            ->join('hasil_ujian', 'hasil_ujian.ujian_id = ujian.id', 'left')
            ->join('soal', 'soal.bank_soal_id = ujian.bank_soal_id', 'left')
            ->groupBy('ujian.id')
            ->orderBy('ujian.created_at', 'DESC');

        if ($userId !== null) {
            $builder->where('ujian.created_by', $userId);
        }

        return $builder->findAll();
    }


    /* ===============================
       GET JADWAL UJIAN TERDEKAT
    =============================== */
    public function getJadwalUjianTerdekat($userId = null)
    {
        $builder = $this->select('
                ujian.*,
                bank_soal.nama AS nama_bank_soal,
                jenis_ujian.nama AS nama_jenis_ujian
            ')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->join('jenis_ujian', 'jenis_ujian.id = ujian.jenis_ujian_id', 'left')
            ->where('ujian.is_active', 1)
            ->orderBy('ujian.waktu_mulai', 'ASC')
            ->limit(5);

        if ($userId !== null) {
            $builder->where('ujian.created_by', $userId);
        }

        return $builder->findAll();
    }


    /* ===============================
       GET UJIAN UNTUK PESERTA (FULL)
    =============================== */
    public function getAllPeserta($pesertaId)
    {
        return $this->select('
                ujian.id,
                ujian.bank_soal_id,
                ujian.jenis_ujian_id,
                ujian.nama_ujian,
                ujian.kode_ujian,
                ujian.acak_soal,
                ujian.acak_opsi,
                ujian.pakai_token,
                ujian.durasi_ujian,
                ujian.minimal_durasi,
                ujian.tampil_nilai,
                ujian.tampil_pembahasan,
                ujian.pakai_webcam,
                ujian.perangkat_terkunci,
                ujian.waktu_mulai,
                ujian.waktu_selesai,
                ujian.is_active,
                ujian.created_at,
                ujian.updated_at,
                bank_soal.nama as nama_bank_soal,
                hasil_ujian.device_id,
                jenis_ujian.nama AS nama_jenis_ujian
            ')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->join('jenis_ujian', 'jenis_ujian.id = ujian.jenis_ujian_id', 'left')
            ->join('hasil_ujian', 'hasil_ujian.ujian_id = ujian.id', 'left')
            ->where('hasil_ujian.peserta_id', $pesertaId)
            ->groupBy('ujian.id')
            ->orderBy('ujian.created_at', 'DESC')
            ->findAll();
    }


    /* ===============================
       GET UJIAN UNTUK PESERTA (RINGAN)
    =============================== */
    public function getAllPesertaRingan($pesertaId)
    {
        $db = \Config\Database::connect();

        // Subquery: daftar ujian_id yang diikuti peserta
        $subQuery = $db->table('hasil_ujian')
            ->select('ujian_id')
            ->where('peserta_id', $pesertaId)
            ->getCompiledSelect();

        // Query utama
        return $this->select('
                ujian.id,
                ujian.bank_soal_id,
                ujian.jenis_ujian_id,
                ujian.nama_ujian,
                ujian.kode_ujian,
                ujian.durasi_ujian,
                ujian.pakai_token,
                ujian.waktu_mulai,
                ujian.waktu_selesai,
                ujian.is_active,
                bank_soal.nama as nama_bank_soal,
                jenis_ujian.nama AS nama_jenis_ujian
            ')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id', 'left')
            ->join('jenis_ujian', 'jenis_ujian.id = ujian.jenis_ujian_id', 'left')
            ->where("ujian.id IN ($subQuery)", null, false)
            ->orderBy('ujian.created_at', 'DESC')
            ->findAll();
    }


    /* ===============================
       GET DETAIL UJIAN
    =============================== */
    public function getWithNamaMapel($id)
    {
        return $this->select('
                ujian.*,
                bank_soal.nama as nama_mapel,
                jenis_ujian.nama AS nama_jenis_ujian
            ')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id', 'left')
            ->join('jenis_ujian', 'jenis_ujian.id = ujian.jenis_ujian_id', 'left')
            ->where('ujian.id', $id)
            ->first();
    }
}
