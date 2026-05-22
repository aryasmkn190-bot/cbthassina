<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSesiUjianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'CHAR', 'constraint' => 36],
            'nama'     => ['type' => 'VARCHAR', 'constraint' => 100],
            'waktu_mulai'   => ['type' => 'TIME', 'null' => true],
            'waktu_selesai' => ['type' => 'TIME', 'null' => true],
            'keterangan'    => ['type' => 'TEXT', 'null' => true],
            'is_active'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('sesi');
    }

    public function down()
    {
        $this->forge->dropTable('sesi');
    }
}
