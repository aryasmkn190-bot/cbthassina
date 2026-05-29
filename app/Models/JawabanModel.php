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

    public function saveJawabanCached(string $ujianId, ?string $pesertaId, array $jawabanInput, ?string $guestId = null)
    {
        $cache = service('cache');
        $cacheClass = get_class($cache);
        $isDummy = (strpos($cacheClass, 'DummyHandler') !== false);
        $cacheSaved = false;

        if (!$isDummy) {
            try {
                $id = $pesertaId ?: $guestId;
                $cacheKey = "ujian:jawaban:{$ujianId}:{$id}";

                $answers = $cache->get($cacheKey);
                if ($answers === null) {
                    $answers = $this->getJawabanCached($ujianId, $pesertaId, $guestId);
                }

                foreach ($jawabanInput as $soalId => $jawaban) {
                    $answers[$soalId] = [
                        'soal_id' => $soalId,
                        'jawaban' => json_encode($jawaban, JSON_UNESCAPED_UNICODE)
                    ];
                }

                $cacheSaved = $cache->save($cacheKey, $answers, 14400);
            } catch (\Throwable $e) {
                log_message('error', 'Redis cache save failed, falling back to MySQL: ' . $e->getMessage());
                $cacheSaved = false;
            }
        }

        // Fallback: Write directly to the MySQL database if cache fails or is dummy
        if (!$cacheSaved) {
            foreach ($jawabanInput as $soalId => $jawaban) {
                $data = [
                    'ujian_id' => $ujianId,
                    'peserta_id' => $pesertaId,
                    'guest_id' => $guestId,
                    'soal_id' => $soalId,
                    'jawaban' => json_encode($jawaban, JSON_UNESCAPED_UNICODE),
                    'skor' => 0
                ];
                $this->saveJawaban($data);
            }
        }
    }

    public function getJawabanCached(string $ujianId, ?string $pesertaId, ?string $guestId = null): array
    {
        $cache = service('cache');
        $cacheClass = get_class($cache);
        $isDummy = (strpos($cacheClass, 'DummyHandler') !== false);
        $answers = null;

        if (!$isDummy) {
            try {
                $id = $pesertaId ?: $guestId;
                $cacheKey = "ujian:jawaban:{$ujianId}:{$id}";
                $answers = $cache->get($cacheKey);
            } catch (\Throwable $e) {
                log_message('error', 'Redis cache get failed, falling back to MySQL: ' . $e->getMessage());
            }
        }

        if ($answers === null) {
            // Load directly from MySQL
            $query = $this->where('ujian_id', $ujianId);
            if ($pesertaId) {
                $query = $query->where('peserta_id', $pesertaId);
            } else {
                $query = $query->where('guest_id', $guestId);
            }
            $rows = $query->findAll();

            $answers = [];
            foreach ($rows as $row) {
                $answers[$row['soal_id']] = [
                    'soal_id' => $row['soal_id'],
                    'jawaban' => $row['jawaban']
                ];
            }

            // Save back to cache if cache is working
            if (!$isDummy && !empty($answers)) {
                try {
                    $id = $pesertaId ?: $guestId;
                    $cacheKey = "ujian:jawaban:{$ujianId}:{$id}";
                    $cache->save($cacheKey, $answers, 14400);
                } catch (\Throwable $e) {
                    // Ignore cache write error
                }
            }
        }

        return $answers;
    }

    public function deleteJawabanCached(string $ujianId, ?string $pesertaId, ?string $guestId = null)
    {
        $cache = service('cache');
        $cacheClass = get_class($cache);
        $isDummy = (strpos($cacheClass, 'DummyHandler') !== false);

        if (!$isDummy) {
            try {
                $id = $pesertaId ?: $guestId;
                $cacheKey = "ujian:jawaban:{$ujianId}:{$id}";
                $cache->delete($cacheKey);
            } catch (\Throwable $e) {
                log_message('error', 'Redis cache delete failed: ' . $e->getMessage());
            }
        }

        // Always clean up from MySQL
        $query = $this->where('ujian_id', $ujianId);
        if ($pesertaId) {
            $query = $query->where('peserta_id', $pesertaId);
        } else {
            $query = $query->where('guest_id', $guestId);
        }
        $query->delete();
    }
}
