<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExambroBlockSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'package_name' => 'com.facebook.katana',
                'app_name'     => 'Facebook',
                'category'     => 'Sosial Media',
                'is_blocked'   => true,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'package_name' => 'com.instagram.android',
                'app_name'     => 'Instagram',
                'category'     => 'Sosial Media',
                'is_blocked'   => true,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
            [
                'package_name' => 'com.mobile.legends',
                'app_name'     => 'Mobile Legends',
                'category'     => 'Game',
                'is_blocked'   => true,
                'created_at'   => date('Y-m-d H:i:s'),
            ],
        ];

        $this->db->table('exambro_block')->insertBatch($data);
    }
}
