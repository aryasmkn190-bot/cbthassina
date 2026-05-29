<?php

namespace App\Models;

use CodeIgniter\Model;

class PesertaModel extends Model
{
    protected $table            = 'peserta';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id',
        'nama',
        'nisn',
        'nis',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'telepon',
        'email',
        'alamat',
        'rt',
        'rw',
        'dusun',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'jenis_tinggal',
        'alat_transportasi',
        'nama_ayah',
        'nik_ayah',
        'tahun_lahir_ayah',
        'pendidikan_ayah',
        'pekerjaan_ayah',
        'penghasilan_ayah',
        'nama_ibu',
        'nik_ibu',
        'tahun_lahir_ibu',
        'pendidikan_ibu',
        'pekerjaan_ibu',
        'penghasilan_ibu',
        'nama_wali',
        'nik_wali',
        'tahun_lahir_wali',
        'pendidikan_wali',
        'pekerjaan_wali',
        'penghasilan_wali',
        'tingkat_id',
        'kelas_id',
        'jurusan_id',
        'agama_id',
        'ruang_id',
        'sesi_id',
        'username',
        'password',
        'api_token',
        'is_active',
        'last_login',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    public function getWithRelations()
    {
        return $this->select('
            peserta.*,
            tingkat.nama AS tingkat,
            kelas.nama AS kelas,
            jurusan.nama AS jurusan,
            agama.nama AS agama
        ')
            ->join('tingkat', 'tingkat.id = peserta.tingkat_id', 'left')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = peserta.jurusan_id', 'left')
            ->join('agama', 'agama.id = peserta.agama_id', 'left')
            ->orderBy('peserta.username')
            ->findAll();
    }
    public function getByIdentity($identity)
    {
        return $this->select('peserta.*, kelas.nama AS nama_kelas, jurusan.nama as nama_jurusan')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->join('jurusan', 'jurusan.id = peserta.jurusan_id', 'left')
            ->where('username', $identity)
            ->orWhere('nisn', $identity)
            ->first();
    }
    public function getAllWithKelas()
    {
        return $this->select('peserta.*, kelas.nama AS nama_kelas')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->findAll();
    }

    public function getByIdWithKelas($id)
    {
        return $this->select('peserta.*, kelas.nama AS nama_kelas')
            ->join('kelas', 'kelas.id = peserta.kelas_id', 'left')
            ->where('peserta.id', $id)
            ->first();
    }
}
