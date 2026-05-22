<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBankSoalTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'kode'         => ['type' => 'VARCHAR', 'constraint' => 50],
            'nama'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'    => ['type' => 'TEXT', 'null' => true],
            'created_by'   => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],

            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'is_public'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->addKey('created_by');

        // Uncomment jika punya tabel user atau guru
        $this->forge->addForeignKey('created_by', 'users', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('bank_soal');
    }

    public function down()
    {
        $this->forge->dropTable('bank_soal');
    }
}
