<?php

namespace App\Models;

use CodeIgniter\Model;

class EkstraModel extends Model
{
    protected $table            = 'ekstrakurikuler';
    protected $primaryKey       = 'id';
    protected $useAutoIncrement = false;
    protected $returnType       = 'array';
    protected $useTimestamps    = true;
    protected $createdField     = 'created_at';
    protected $updatedField     = 'updated_at';
    protected $allowedFields    = [
        'id', 'nama_ekstra', 'pembina_nama', 'jadwal_hari', 'waktu'
    ];
}
