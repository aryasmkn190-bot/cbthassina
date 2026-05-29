<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTugasJawabanTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'            => ['type' => 'CHAR', 'constraint' => 36],
            'tugas_id'      => ['type' => 'CHAR', 'constraint' => 36],
            'peserta_id'    => ['type' => 'CHAR', 'constraint' => 36],
            'file_path'     => ['type' => 'VARCHAR', 'constraint' => 255],
            'catatan_guru'  => ['type' => 'TEXT', 'null' => true],
            'nilai'         => ['type' => 'INT', 'constraint' => 3, 'default' => 0],
            'tanggal_kirim' => ['type' => 'DATETIME'],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['tugas_id', 'peserta_id']);
        $this->forge->createTable('tugas_jawaban');

        $this->db->query("
            ALTER TABLE `tugas_jawaban`
            ADD CONSTRAINT `fk_jawaban_tugas`
                FOREIGN KEY (`tugas_id`) REFERENCES `tugas` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE,
            ADD CONSTRAINT `fk_jawaban_peserta`
                FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("
            ALTER TABLE `tugas_jawaban`
            DROP FOREIGN KEY `fk_jawaban_tugas`,
            DROP FOREIGN KEY `fk_jawaban_peserta`
        ");
        $this->forge->dropTable('tugas_jawaban');
    }
}
