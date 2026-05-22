<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;
use Ramsey\Uuid\Uuid;

class UserSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'id' => Uuid::uuid4()->toString(),
                'username' => 'admin',
                'email' => 'admin@example.com',
                'full_name' => 'Administrator Utama',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'is_active' => 1,
                'roles' => 'admin,superuser',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'username' => 'pengawas',
                'email' => 'pengawas@example.com',
                'full_name' => 'Pengawas Ujian',
                'password' => password_hash('pengawas123', PASSWORD_DEFAULT),
                'is_active' => 1,
                'roles' => 'pengawas',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
            [
                'id' => Uuid::uuid4()->toString(),
                'username' => 'siswa1',
                'email' => 'siswa1@example.com',
                'full_name' => 'Siswa Pertama',
                'password' => password_hash('siswa123', PASSWORD_DEFAULT),
                'is_active' => 1,
                'roles' => 'siswa',
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('users')->insertBatch($data);
    }
}
