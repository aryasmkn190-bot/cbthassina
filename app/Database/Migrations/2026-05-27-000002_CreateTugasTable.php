<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTugasTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'CHAR', 'constraint' => 36],
            'kelas_id'       => ['type' => 'CHAR', 'constraint' => 36],
            'judul'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'      => ['type' => 'TEXT', 'null' => true],
            'tenggat_waktu'  => ['type' => 'DATETIME'],
            'mata_pelajaran' => ['type' => 'VARCHAR', 'constraint' => 100],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kelas_id');
        $this->forge->createTable('tugas');

        $this->db->query("
            ALTER TABLE `tugas`
            ADD CONSTRAINT `fk_tugas_kelas`
                FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `tugas` DROP FOREIGN KEY `fk_tugas_kelas`");
        $this->forge->dropTable('tugas');
    }
}
