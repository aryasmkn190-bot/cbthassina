<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTopikSoal extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'CHAR', 'constraint' => 36],
            'bank_soal_id'  => ['type' => 'CHAR', 'constraint' => 36],
            'nama'          => ['type' => 'VARCHAR', 'constraint' => 100],
            'keterangan'    => ['type' => 'TEXT', 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bank_soal_id', 'bank_soal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('topik_soal');
    }

    public function down()
    {
        $this->forge->dropTable('topik_soal');
    }
}
