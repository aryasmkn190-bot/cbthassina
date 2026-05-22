<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class ExambroMenuSeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'title'       => 'Ujian 1',
                'link'        => 'https://cbtcandy.com',
                'icon'        => 'home',
                'is_active'   => true,
                'order'       => 1,
                'token'       => 'abc123',
                'is_token'    => true,
                'tgl_dibuka'  => '2025-06-22 07:00:00',
                'tgl_ditutup' => '2025-06-22 10:00:00',
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ],
            [
                'title'       => 'Ujian 2',
                'link'        => 'https://sumatif.smkhsagung.sch.id',
                'icon'        => 'file-text',
                'is_active'   => true,
                'order'       => 2,
                'token'       => null,
                'is_token'    => false,
                'tgl_dibuka'  => null,
                'tgl_ditutup' => null,
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]
        ];

        $this->db->table('exambro_menu')->insertBatch($data);
    }
}
