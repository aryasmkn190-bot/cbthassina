<?php

namespace App\Models;

use CodeIgniter\Model;

class ExambroMenuModel extends Model
{
    protected $table      = 'exambro_menu';
    protected $primaryKey = 'id';

    protected $useAutoIncrement = true;

    protected $allowedFields = [
        'title',
        'link',
        'icon',
        'is_active',
        'order',
        'token',
        'is_token',
        'tgl_dibuka',
        'tgl_ditutup',
        'created_at',
        'updated_at',
    ];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function getActiveMenus()
    {
        return $this->where('is_active', 1)
            ->orderBy('order', 'ASC')
            ->findAll();
    }
}
