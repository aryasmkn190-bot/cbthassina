<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateJadwalPelajaranTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'CHAR', 'constraint' => 36],
            'kelas_id'       => ['type' => 'CHAR', 'constraint' => 36],
            'mata_pelajaran' => ['type' => 'VARCHAR', 'constraint' => 100],
            'hari'           => ['type' => 'ENUM', 'constraint' => ['senin', 'selasa', 'rabu', 'kamis', 'jumat', 'sabtu']],
            'waktu_mulai'    => ['type' => 'TIME'],
            'waktu_selesai'  => ['type' => 'TIME'],
            'guru_nama'      => ['type' => 'VARCHAR', 'constraint' => 100],
            'ruangan'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('kelas_id');
        $this->forge->createTable('jadwal_pelajaran');
        
        $this->db->query("
            ALTER TABLE `jadwal_pelajaran`
            ADD CONSTRAINT `fk_jadwal_kelas`
                FOREIGN KEY (`kelas_id`) REFERENCES `kelas` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `jadwal_pelajaran` DROP FOREIGN KEY `fk_jadwal_kelas`");
        $this->forge->dropTable('jadwal_pelajaran');
    }
}
