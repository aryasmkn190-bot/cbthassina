<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class MediaFileModel extends Model
{
    protected $table            = 'media_files';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;

    protected $allowedFields    = [
        'id',
        'path',
        'type',
        'mime_type',
        'used_in_soal',
        'created_at',
        'updated_at',
    ];

    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';

    protected $returnType       = 'array';

    // UUIDv4 generator
    protected function insertID(&$data)
    {
        if (!isset($data['id'])) {
            $data['id'] = Uuid::uuid4()->toString();
        }
    }

    protected $beforeInsert = ['generateUUID'];

    protected function generateUUID(array $data)
    {
        $this->insertID($data['data']);
        return $data;
    }

    /**
     * Simpan media baru jika belum ada.
     */
    public function saveMedia($path, $type, $mime = null, $usedInSoal = null)
    {
        return $this->insert([
            'path'         => $path,
            'type'         => $type,
            'mime_type'    => $mime,
            'used_in_soal' => $usedInSoal
        ]);
    }

    /**
     * Cari media berdasarkan path
     */
    public function findByPath($path)
    {
        return $this->where('path', $path)->first();
    }

    /**
     * Hapus berdasarkan path
     */
    public function deleteByPath($path)
    {
        return $this->where('path', $path)->delete();
    }
}
