<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class SettingsExambroSeeder extends Seeder
{
    public function run()
    {
        $data = [
            'logo_resource'      => 'logo.png',
            'banner_img'         => 'banner.jpg',
            'default_brightness' => 95,
            'bell_sound'         => 'bell_sound.mp3',
            'exit_sound'         => 'exit_sound.mp3',
            'app_volume'         => 80,
            'school_name'        => 'SMK CANDY EXAM',
            'app_name'           => 'Candy Exam Browser',
            'version'            => '2.0.0',
            'password_exit'      => 'candy123',
            'secret_code'        => '$2a$09$gs6X2MIXdFe2VR7IvzlVc.7UyKJ6GNT6RcdazukThmrcAQV50pA9O',
            'theme_color'        => '#2196F3',
            'user_agent'         => 'CandyCBTBro',
            'informasi'          => 'Selamat datang di Candy Exam!',
            'menu_url'           => true,
            'menu_scanqr'        => true,
            'bluetooth'          => true,
            'mode_exam'          => true,
            'headset'            => true,
            'updated_at'         => date('Y-m-d H:i:s'),
        ];

        $this->db->table('settings_exambro')->insert($data);
    }
}
