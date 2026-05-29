<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateRaporNilaiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'CHAR', 'constraint' => 36],
            'peserta_id'     => ['type' => 'CHAR', 'constraint' => 36],
            'mata_pelajaran' => ['type' => 'VARCHAR', 'constraint' => 100],
            'nilai'          => ['type' => 'INT', 'constraint' => 3],
            'grade'          => ['type' => 'CHAR', 'constraint' => 2],
            'semester'       => ['type' => 'VARCHAR', 'constraint' => 20],
            'tahun_ajaran'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['peserta_id', 'mata_pelajaran']);
        $this->forge->createTable('rapor_nilai');

        $this->db->query("
            ALTER TABLE `rapor_nilai`
            ADD CONSTRAINT `fk_rapor_peserta`
                FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `rapor_nilai` DROP FOREIGN KEY `fk_rapor_peserta`");
        $this->forge->dropTable('rapor_nilai');
    }
}
