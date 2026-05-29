<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddWaliKelasAndGuruToJadwal extends Migration
{
    public function up()
    {
        // 1. Add wali_kelas_id to kelas and apply FK constraint
        $this->db->query("ALTER TABLE `kelas` ADD COLUMN `wali_kelas_id` CHAR(36) NULL AFTER `nama`");
        $this->db->query("ALTER TABLE `kelas` ADD CONSTRAINT `fk_kelas_wali` FOREIGN KEY (`wali_kelas_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL");

        // 2. Add guru_id to jadwal_pelajaran and apply FK constraint
        $this->db->query("ALTER TABLE `jadwal_pelajaran` ADD COLUMN `guru_id` CHAR(36) NULL AFTER `guru_nama`");
        $this->db->query("ALTER TABLE `jadwal_pelajaran` ADD CONSTRAINT `fk_jadwal_guru` FOREIGN KEY (`guru_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE ON DELETE SET NULL");

        // 3. Make kelas_id nullable in peserta table
        $this->db->query("ALTER TABLE `peserta` MODIFY COLUMN `kelas_id` CHAR(36) NULL");
    }

    public function down()
    {
        // 1. Drop constraints and columns from kelas
        $this->db->query("ALTER TABLE `kelas` DROP FOREIGN KEY `fk_kelas_wali`");
        $this->db->query("ALTER TABLE `kelas` DROP COLUMN `wali_kelas_id`");

        // 2. Drop constraints and columns from jadwal_pelajaran
        $this->db->query("ALTER TABLE `jadwal_pelajaran` DROP FOREIGN KEY `fk_jadwal_guru`");
        $this->db->query("ALTER TABLE `jadwal_pelajaran` DROP COLUMN `guru_id`");

        // 3. Revert kelas_id to NOT NULL in peserta table
        $this->db->query("ALTER TABLE `peserta` MODIFY COLUMN `kelas_id` CHAR(36) NOT NULL");
    }
}
