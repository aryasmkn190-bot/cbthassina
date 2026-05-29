<?php

namespace App\Models;

use CodeIgniter\Model;
use Ramsey\Uuid\Uuid;

class UserModel extends Model
{
    protected $table      = 'users';
    protected $primaryKey = 'id';
    protected $useAutoIncrement = false;

    // protected $useSoftDeletes   = true;

    protected $allowedFields = [
        'id',
        'username',
        'email',
        'full_name',
        'password',
        'roles',
        'mata_pelajaran_id',
        'is_active',
        'remember_token',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';
    // protected $deletedField  = 'deleted_at';

    protected $returnType    = 'array';
    protected $validationRules    = [];
    protected $validationMessages = [];
    protected $skipValidation     = false;

    /**
     * Generate UUID v4 for primary key
     */
    protected function generateUUID(array $data)
    {
        $data['data']['id'] = Uuid::uuid4()->toString();
        return $data;
    }

    /**
     * Hash password if set
     */
    protected function hashPassword(array $data)
    {
        if (!empty($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }

    /**
     * Login check using email or username
     */
    public function getByIdentity($identity)
    {
        return $this->where('email', $identity)
            ->orWhere('username', $identity)
            ->first();
    }
}
