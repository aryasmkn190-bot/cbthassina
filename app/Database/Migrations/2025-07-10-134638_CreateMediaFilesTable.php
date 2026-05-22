<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMediaFilesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'CHAR', 'constraint' => 36],
            'path'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'type'          => ['type' => 'ENUM', 'constraint' => ['image', 'audio', 'video']],
            'mime_type'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'used_in_soal'  => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('used_in_soal', 'soal', 'id', 'CASCADE', 'SET NULL');
        $this->forge->createTable('media_files');
    }

    public function down()
    {
        $this->forge->dropTable('media_files');
    }
}
