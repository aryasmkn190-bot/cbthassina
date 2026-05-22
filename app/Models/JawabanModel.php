<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class JawabanModel extends Model
{
    protected $table = 'jawaban';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'id',
        'ujian_id',
        'peserta_id',
        'guest_id',
        'soal_id',
        'jawaban',
        'skor',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps = true; // otomatis isi created_at dan updated_at
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    public function updateOrInsert(array $where, array $data)
    {
        $existing = $this->where($where)->first();

        if ($existing) {
            $this->update($existing['id'], $data);
        } else {
            $this->insert($data);
        }
    }

    // Simpan jawaban: insert baru jika belum ada, update jika sudah ada
    public function saveJawaban(array $data)
    {
        $existing = $this->where('ujian_id', $data['ujian_id'])
            ->where('peserta_id', $data['peserta_id'])
            ->where('soal_id', $data['soal_id'])
            ->first();

        if ($existing) {
            return $this->update($existing['id'], [
                'jawaban' => $data['jawaban'],
                'skor' => $data['skor'] ?? 0,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        } else {
            if (!isset($data['id'])) {
                $data['id'] = Uuid::uuid4()->toString();
            }
            if (!isset($data['created_at'])) {
                $data['created_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($data['updated_at'])) {
                $data['updated_at'] = date('Y-m-d H:i:s');
            }
            return $this->insert($data);
        }
    }
}
