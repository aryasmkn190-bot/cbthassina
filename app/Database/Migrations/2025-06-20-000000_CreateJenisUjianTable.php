<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJenisUjianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'         => ['type' => 'CHAR', 'constraint' => 36],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 150],
            'deskripsi'  => ['type' => 'TEXT', 'null' => true],

            // contoh: PTS, PAS, UAS, UKK, Simulasi
            'kode'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],

            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('jenis_ujian');
    }

    public function down()
    {
        $this->forge->dropTable('jenis_ujian');
    }
}
