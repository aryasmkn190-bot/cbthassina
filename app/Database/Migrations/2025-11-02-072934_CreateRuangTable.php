<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRuangTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'CHAR', 'constraint' => 36],
            'nama'         => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan'   => ['type' => 'TEXT', 'null' => true],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('ruang');
    }

    public function down()
    {
        $this->forge->dropTable('ruang');
    }
}
