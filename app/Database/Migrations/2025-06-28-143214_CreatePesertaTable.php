<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreatePesertaTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'nama'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'nisn'         => ['type' => 'VARCHAR', 'constraint' => 20, 'unique' => true],
            'tingkat_id'   => ['type' => 'CHAR', 'constraint' => 36],
            'kelas_id'     => ['type' => 'CHAR', 'constraint' => 36],
            'jurusan_id'   => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'agama_id'     => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'ruang_id'     => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'sesi_id'      => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'username'     => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'password'     => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'api_token'    => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'is_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'last_login'   => ['type' => 'DATETIME', 'null' => true],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('nisn');
        $this->forge->addKey(['tingkat_id', 'kelas_id']);
        $this->forge->addKey(['ruang_id', 'sesi_id']);

        $this->forge->createTable('peserta');

        // Tambahkan foreign key constraint setelah tabel dibuat
        $this->db->query("
            ALTER TABLE `peserta`
            ADD CONSTRAINT `fk_peserta_ruang`
                FOREIGN KEY (`ruang_id`) REFERENCES `ruang` (`id`)
                ON UPDATE CASCADE
                ON DELETE SET NULL,
            ADD CONSTRAINT `fk_peserta_sesi`
                FOREIGN KEY (`sesi_id`) REFERENCES `sesi` (`id`)
                ON UPDATE CASCADE
                ON DELETE SET NULL
        ");
    }

    public function down()
    {
        // Hapus foreign key dulu sebelum drop table
        $this->db->query("
            ALTER TABLE `peserta`
            DROP FOREIGN KEY `fk_peserta_ruang`,
            DROP FOREIGN KEY `fk_peserta_sesi`
        ");

        $this->forge->dropTable('peserta');
    }
}
