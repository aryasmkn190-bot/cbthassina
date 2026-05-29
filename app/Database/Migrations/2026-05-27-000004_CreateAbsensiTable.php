<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateAbsensiTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => ['type' => 'CHAR', 'constraint' => 36],
            'peserta_id'  => ['type' => 'CHAR', 'constraint' => 36],
            'tanggal'     => ['type' => 'DATE'],
            'waktu_scan'  => ['type' => 'TIME'],
            'status'      => ['type' => 'VARCHAR', 'constraint' => 20],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey(['peserta_id', 'tanggal']);
        $this->forge->createTable('absensi');

        $this->db->query("
            ALTER TABLE `absensi`
            ADD CONSTRAINT `fk_absensi_peserta`
                FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `absensi` DROP FOREIGN KEY `fk_absensi_peserta`");
        $this->forge->dropTable('absensi');
    }
}
