<?php

namespace App\Models;

use CodeIgniter\Model;

class HasilUjianModel extends Model
{
    protected $table            = 'hasil_ujian';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $allowedFields    = [
        'id',
        'ujian_id',
        'peserta_id',
        'waktu_mulai',
        'waktu_selesai',
        'nilai_pg',
        'nilai_esai',
        'nilai_total',
        'poin_benar',
        'poin_salah',
        'poin_maksimal',
        'jawaban_json',
        'soal_benar',
        'soal_salah',
        'kosong',
        'status',
        'urutan_opsi',
        'urutan_soal',
        'token_valid',
        'is_device_active',
        'platform',
        'guest_id',
        'device_id',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    /**
     * Ambil hasil ujian lengkap dengan nama peserta dan nama kelas.
     */
    public function getHasilUjianLengkap($ujianId)
    {
        return $this->select("
        hasil_ujian.id,
        hasil_ujian.ujian_id,
        hasil_ujian.peserta_id,
        hasil_ujian.nilai_total,
        hasil_ujian.soal_benar,
        hasil_ujian.soal_salah,
        hasil_ujian.kosong,
        hasil_ujian.waktu_mulai,
        hasil_ujian.waktu_selesai,
        hasil_ujian.status,
        hasil_ujian.platform,
        CASE 
            WHEN hasil_ujian.peserta_id IS NULL 
                THEN CONCAT('Guest_', LEFT(hasil_ujian.guest_id, 8))
            ELSE peserta.nama
        END as nama_peserta,
        COALESCE(peserta.nisn, '-') as nisn,
        COALESCE(kelas.nama, '-') as nama_kelas
    ")
            ->join('peserta', 'peserta.id = hasil_ujian.peserta_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->where('hasil_ujian.ujian_id', $ujianId)
            ->orderBy('kelas.nama', 'ASC')       // ✅ kelas dulu
            ->orderBy('peserta.nama', 'ASC')     // ✅ baru nama peserta
            ->findAll();
    }




    public function getSkoringByUjianId($ujianId, $filters = [])
    {
        $query = $this->select('
            hasil_ujian.*, 
            peserta.nama AS nama_peserta,
            peserta.nisn,
            kelas.nama AS nama_kelas,
            ujian.nama_ujian,
            bank_soal.nama
        ')
            ->join('peserta', 'peserta.id = hasil_ujian.peserta_id')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->join('ujian', 'ujian.id = hasil_ujian.ujian_id')
            ->join('bank_soal', 'bank_soal.id = ujian.bank_soal_id')
            ->where('hasil_ujian.ujian_id', $ujianId);

        if (!empty($filters['kelas'])) {
            $query->where('kelas.nama', $filters['kelas']);
        }
        if (!empty($filters['status'])) {
            $query->where('hasil_ujian.status', $filters['status']);
        }
        if (!empty($filters['search'])) {
            $query->groupStart()
                ->like('peserta.nama', $filters['search'])
                ->orLike('peserta.nisn', $filters['search'])
                ->groupEnd();
        }

        return $query->orderBy('kelas.nama', 'ASC')       // ✅ kelas dulu
            ->orderBy('peserta.nama', 'ASC')     // ✅ baru nama peserta
            ->findAll();
    }


    public function resetUjian($id)
    {
        return $this->update($id, [
            'waktu_mulai'   => null,
            'waktu_selesai' => null,
            'nilai_pg'      => 0,
            'nilai_esai'    => 0,
            'nilai_total'   => 0,
            'poin_benar'   => 0,
            'poin_maksimal' => 0,
            'poin_salah' => 0,
            'jawaban_json' => null,
            'soal_benar'         => 0,
            'soal_salah'         => 0,
            'kosong'        => 0,
            'urutan_opsi'        => null,
            'urutan_soal'        => null,
            'status'        => 'belum_mulai',
            'platform'        => 'web',
            'token_valid'   => 0,
            'is_device_active'   => 0,
            'device_id' => null,
            'updated_at'    => date('Y-m-d H:i:s')
        ]);
    }

    public function tandaiSelesai($id)
    {
        return $this->update($id, [
            'status'     => 'selesai',
            'updated_at' => date('Y-m-d H:i:s')
        ]);
    }
}
