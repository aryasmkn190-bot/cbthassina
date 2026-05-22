<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJawabanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'ujian_id' => ['type' => 'CHAR', 'constraint' => 36],
            'peserta_id'     => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'guest_id'     => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'soal_id' => ['type' => 'CHAR', 'constraint' => 36],
            'jawaban' => ['type' => 'TEXT', 'null' => true],
            'skor' => ['type' => 'FLOAT', 'null' => true, 'default' => 0],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey(['ujian_id', 'peserta_id', 'soal_id']);

        // Optional: Jika sudah yakin semua FK ada
        $this->forge->addForeignKey('ujian_id', 'ujian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('peserta_id', 'peserta', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('soal_id', 'soal', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('jawaban');
    }

    public function down()
    {
        $this->forge->dropTable('jawaban');
    }
}
