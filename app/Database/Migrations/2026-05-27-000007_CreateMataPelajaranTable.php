<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateMataPelajaranTable extends Migration
{
    public function up()
    {
        // 1. Create mata_pelajaran table
        $this->forge->addField([
            'id'         => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'kode'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'nama'       => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'is_active'  => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'DATETIME', 'null' => true],
            'updated_at' => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode');
        $this->forge->createTable('mata_pelajaran');

        // 2. Add columns to other tables
        $this->forge->addColumn('users', [
            'mata_pelajaran_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => true, 'after' => 'roles']
        ]);

        $this->forge->addColumn('bank_soal', [
            'mata_pelajaran_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => true, 'after' => 'created_by']
        ]);

        $this->forge->dropColumn('jadwal_pelajaran', 'mata_pelajaran');
        $this->forge->addColumn('jadwal_pelajaran', [
            'mata_pelajaran_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => true, 'after' => 'kelas_id']
        ]);

        $this->forge->dropColumn('tugas', 'mata_pelajaran');
        $this->forge->addColumn('tugas', [
            'mata_pelajaran_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => true, 'after' => 'kelas_id']
        ]);

        $this->forge->dropColumn('rapor_nilai', 'mata_pelajaran');
        $this->forge->addColumn('rapor_nilai', [
            'mata_pelajaran_id' => ['type' => 'CHAR', 'constraint' => 36, 'null' => true, 'after' => 'peserta_id']
        ]);

        // 3. Add foreign key constraints using raw queries
        $this->db->query("ALTER TABLE `users` ADD CONSTRAINT `fk_users_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON UPDATE CASCADE ON DELETE SET NULL");
        $this->db->query("ALTER TABLE `bank_soal` ADD CONSTRAINT `fk_bank_soal_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON UPDATE CASCADE ON DELETE SET NULL");
        $this->db->query("ALTER TABLE `jadwal_pelajaran` ADD CONSTRAINT `fk_jadwal_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON UPDATE CASCADE ON DELETE SET NULL");
        $this->db->query("ALTER TABLE `tugas` ADD CONSTRAINT `fk_tugas_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON UPDATE CASCADE ON DELETE SET NULL");
        $this->db->query("ALTER TABLE `rapor_nilai` ADD CONSTRAINT `fk_rapor_mapel` FOREIGN KEY (`mata_pelajaran_id`) REFERENCES `mata_pelajaran` (`id`) ON UPDATE CASCADE ON DELETE SET NULL");
        
        $this->db->query("ALTER TABLE `rapor_nilai` ADD KEY `idx_rapor_peserta_mapel` (`peserta_id`, `mata_pelajaran_id`)");
    }

    public function down()
    {
        // 1. Drop foreign keys and indices
        $this->db->query("ALTER TABLE `users` DROP FOREIGN KEY `fk_users_mapel`");
        $this->db->query("ALTER TABLE `bank_soal` DROP FOREIGN KEY `fk_bank_soal_mapel`");
        $this->db->query("ALTER TABLE `jadwal_pelajaran` DROP FOREIGN KEY `fk_jadwal_mapel`");
        $this->db->query("ALTER TABLE `tugas` DROP FOREIGN KEY `fk_tugas_mapel`");
        $this->db->query("ALTER TABLE `rapor_nilai` DROP FOREIGN KEY `fk_rapor_mapel`");
        $this->db->query("ALTER TABLE `rapor_nilai` DROP KEY `idx_rapor_peserta_mapel`");

        // 2. Drop added columns and restore previous schema
        $this->forge->dropColumn('users', 'mata_pelajaran_id');
        $this->forge->dropColumn('bank_soal', 'mata_pelajaran_id');
        
        $this->forge->dropColumn('jadwal_pelajaran', 'mata_pelajaran_id');
        $this->forge->addColumn('jadwal_pelajaran', [
            'mata_pelajaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'after' => 'kelas_id']
        ]);

        $this->forge->dropColumn('tugas', 'mata_pelajaran_id');
        $this->forge->addColumn('tugas', [
            'mata_pelajaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'after' => 'tenggat_waktu']
        ]);

        $this->forge->dropColumn('rapor_nilai', 'mata_pelajaran_id');
        $this->forge->addColumn('rapor_nilai', [
            'mata_pelajaran' => ['type' => 'VARCHAR', 'constraint' => 100, 'after' => 'peserta_id']
        ]);
        
        $this->db->query("ALTER TABLE `rapor_nilai` ADD KEY `peserta_id_mata_pelajaran` (`peserta_id`, `mata_pelajaran`)");

        // 3. Drop mata_pelajaran table
        $this->forge->dropTable('mata_pelajaran');
    }
}
