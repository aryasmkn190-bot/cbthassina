<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoalOpsiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'CHAR', 'constraint' => 36],
            'soal_id'     => ['type' => 'CHAR', 'constraint' => 36],

            // Untuk semua jenis
            'label'       => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true], // A, B, Kiri1, etc
            'teks'        => ['type' => 'LONGTEXT', 'null' => true],  // Isi pilihan atau pasangan kiri/kanan
            'pasangan'    => ['type' => 'TEXT', 'null' => true],  // Untuk "jodohkan", pasangan dari label ini
            'is_true'     => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0], // untuk pg, benar/salah, mpg
            'bobot'       => ['type' => 'FLOAT', 'default' => 1],

            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('soal_id', 'soal', 'id', 'CASCADE', 'CASCADE');
        $this->forge->createTable('soal_opsi');
    }

    public function down()
    {
        $this->forge->dropTable('soal_opsi');
    }
}
