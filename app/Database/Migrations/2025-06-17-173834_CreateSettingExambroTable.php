<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsExambroTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                 => ['type' => 'INT', 'auto_increment' => true],
            'logo_resource'      => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'banner_img'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'default_brightness' => ['type' => 'INT', 'default' => 95],
            'bell_sound'         => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'bell_sound.mp3'],
            'exit_sound'         => ['type' => 'VARCHAR', 'constraint' => 255, 'default' => 'exit_sound.mp3'],
            'app_volume'         => ['type' => 'INT', 'default' => 80],
            'school_name'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'app_name'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'version'            => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => 'Version 1.0'],
            'password_exit'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'secret_code'        => ['type' => 'VARCHAR', 'constraint' => 100],
            'theme_color'        => ['type' => 'VARCHAR', 'constraint' => 50, 'default' => "#2196F3"],
            'user_agent'        => ['type' => 'VARCHAR', 'constraint' => 200, 'default' => "CandyCBTBro"],
            'file_exam_config'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'file_exam_config_upload'        => ['type' => 'VARCHAR', 'constraint' => 200],
            'informasi'         => ['type' => 'TEXT'],
            'menu_url'           => ['type' => 'BOOLEAN', 'default' => true],
            'menu_scanqr'        => ['type' => 'BOOLEAN', 'default' => true],
            'bluetooth'          => ['type' => 'BOOLEAN', 'default' => true],
            'mode_exam'          => ['type' => 'BOOLEAN', 'default' => true],
            'headset'            => ['type' => 'BOOLEAN', 'default' => true],
            'restrict_user_agent' => ['type' => 'BOOLEAN', 'default' => false],
            'portal_ujian' => ['type' => 'BOOLEAN', 'default' => false],
            'login_nopassword' => ['type' => 'BOOLEAN', 'default' => false],
            'updated_at'         => ['type' => 'DATETIME', 'null' => true],
        ]);


        $this->forge->addKey('id', true);
        $this->forge->createTable('settings_exambro');
    }

    public function down()
    {
        $this->forge->dropTable('settings_exambro');
    }
}
