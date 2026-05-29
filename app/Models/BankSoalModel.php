<?php

namespace App\Models;

use CodeIgniter\Model;

class BankSoalModel extends Model
{
    protected $table            = 'bank_soal';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;

    protected $returnType       = 'array';
    protected $useSoftDeletes   = false;

    protected $allowedFields    = [
        'id',
        'kode',
        'nama',
        'deskripsi',
        'created_by',
        'mata_pelajaran_id',
        'is_active',
        'is_public',
        'created_at',
        'updated_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    public function getAll($userid = null)
    {
        // Ambil data bank soal utama
        $builder = $this->db->table($this->table . ' bs')
            ->select('bs.*, mp.nama as mata_pelajaran_nama')
            ->join('mata_pelajaran mp', 'mp.id = bs.mata_pelajaran_id', 'left')
            ->orderBy('bs.created_at', 'DESC');

        if ($userid) {
            $builder->where('bs.created_by', $userid);
        }

        $get = $builder->get()->getResultArray();

        $ids = array_column($get, 'id');
        if (empty($ids)) return [];

        // Ambil jumlah soal per jenis
        $soalStats = $this->db->table('soal')
            ->select("
            bank_soal_id,
            COUNT(DISTINCT id) as jumlah_total_soal,
            COUNT(DISTINCT CASE WHEN jenis_soal = 'pg' THEN id END) as jumlah_pg,
            COUNT(DISTINCT CASE WHEN jenis_soal = 'mpg' THEN id END) as jumlah_mpg,
            COUNT(DISTINCT CASE WHEN jenis_soal = 'benar_salah' THEN id END) as jumlah_bs,
            COUNT(DISTINCT CASE WHEN jenis_soal = 'jodohkan' THEN id END) as jumlah_jodohkan,
            COUNT(DISTINCT CASE WHEN jenis_soal = 'isian' THEN id END) as jumlah_isian,
            COUNT(DISTINCT CASE WHEN jenis_soal = 'esai' THEN id END) as jumlah_esai,
            COALESCE(SUM(bobot), 0) as total_bobot
        ")
            ->whereIn('bank_soal_id', $ids)
            ->groupBy('bank_soal_id')
            ->get()
            ->getResultArray();

        $soalStatMap = [];
        foreach ($soalStats as $stat) {
            $soalStatMap[$stat['bank_soal_id']] = $stat;
        }

        // Ambil jumlah dan daftar topik
        $topikData = $this->db->table('topik_soal')
            ->select('bank_soal_id, nama')
            ->whereIn('bank_soal_id', $ids)
            ->get()
            ->getResultArray();

        $topikGrouped = [];
        foreach ($topikData as $t) {
            $topikGrouped[$t['bank_soal_id']][] = $t['nama'];
        }

        // Gabungkan semua data ke array utama
        foreach ($get as &$item) {
            $id = $item['id'];
            $item += $soalStatMap[$id] ?? [
                'jumlah_total_soal' => 0,
                'jumlah_pg' => 0,
                'jumlah_mpg' => 0,
                'jumlah_bs' => 0,
                'jumlah_jodohkan' => 0,
                'jumlah_isian' => 0,
                'jumlah_esai' => 0,
                'total_bobot' => 0,
            ];
            $item['jumlah_topik'] = isset($topikGrouped[$id]) ? count($topikGrouped[$id]) : 0;
            $item['daftar_topik'] = $topikGrouped[$id] ?? [];
        }

        return $get;
    }
}
