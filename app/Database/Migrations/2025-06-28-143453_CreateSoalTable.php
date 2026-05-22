<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSoalTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'topik_soal_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'bank_soal_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'soal_no' => ['type' => 'INT', 'unsigned' => true, 'null' => true],
            'jenis_soal' => ['type' => 'ENUM', 'constraint' => ['pg', 'mpg', 'jodohkan', 'benar_salah', 'esai', 'isian']],
            'pertanyaan' => ['type' => 'LONGTEXT'],
            'jawaban' => ['type' => 'TEXT', 'null' => true],
            'bobot' => ['type' => 'FLOAT', 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('bank_soal_id');
        $this->forge->addForeignKey('bank_soal_id', 'bank_soal', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('soal');
    }

    public function down()
    {
        $this->forge->dropTable('soal');
    }
}
