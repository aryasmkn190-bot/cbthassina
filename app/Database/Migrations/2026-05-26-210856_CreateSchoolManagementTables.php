<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSchoolManagementTables extends Migration
{
    public function up()
    {
        // 1. keuangan_jurnal table
        $this->forge->addField([
            'id'          => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'tanggal'     => ['type' => 'DATE', 'null' => false],
            'keterangan'  => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'tipe'        => ['type' => 'ENUM', 'constraint' => ['debit', 'kredit'], 'null' => false],
            'nominal'     => ['type' => 'DECIMAL', 'constraint' => '15,2', 'null' => false],
            'kategori'    => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('tanggal');
        $this->forge->createTable('keuangan_jurnal');

        // 2. kesiswaan_prestasi table
        $this->forge->addField([
            'id'            => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'peserta_id'    => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'nama_prestasi' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'tingkat'       => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'kategori'      => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'tanggal'       => ['type' => 'DATE', 'null' => false],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('peserta_id');
        $this->forge->createTable('kesiswaan_prestasi');

        // 3. kesiswaan_pelanggaran table
        $this->forge->addField([
            'id'               => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'peserta_id'       => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'nama_pelanggaran' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'kategori'         => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'point'            => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'tanggal'          => ['type' => 'DATE', 'null' => false],
            'tindakan'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'       => ['type' => 'DATETIME', 'null' => true],
            'updated_at'       => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('peserta_id');
        $this->forge->createTable('kesiswaan_pelanggaran');

        // 4. ppdb_pendaftar table
        $this->forge->addField([
            'id'           => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'nomor_daftar' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'nama'         => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'nisn'         => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => false],
            'email'        => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'telepon'      => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'sekolah_asal' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'status'       => ['type' => 'ENUM', 'constraint' => ['menunggu', 'proses', 'diterima', 'ditolak'], 'default' => 'menunggu'],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('nomor_daftar');
        $this->forge->createTable('ppdb_pendaftar');

        // 5. inventaris_barang table
        $this->forge->addField([
            'id'          => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'nama_barang' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => false],
            'kode_barang' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'jumlah'      => ['type' => 'INT', 'constraint' => 11, 'default' => 0],
            'kondisi'     => ['type' => 'ENUM', 'constraint' => ['baik', 'rusak'], 'default' => 'baik'],
            'lokasi'      => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'created_at'  => ['type' => 'DATETIME', 'null' => true],
            'updated_at'  => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('kode_barang');
        $this->forge->createTable('inventaris_barang');

        // 6. ekstrakurikuler table
        $this->forge->addField([
            'id'           => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'nama_ekstra'  => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'pembina_nama' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => false],
            'jadwal_hari'  => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => false],
            'waktu'        => ['type' => 'TIME', 'null' => false],
            'created_at'   => ['type' => 'DATETIME', 'null' => true],
            'updated_at'   => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('ekstrakurikuler');

        // 7. Add foreign key constraints using raw queries
        $this->db->query("ALTER TABLE `kesiswaan_prestasi` ADD CONSTRAINT `fk_prestasi_peserta` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`) ON UPDATE CASCADE ON DELETE CASCADE");
        $this->db->query("ALTER TABLE `kesiswaan_pelanggaran` ADD CONSTRAINT `fk_pelanggaran_peserta` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`) ON UPDATE CASCADE ON DELETE CASCADE");
    }

    public function down()
    {
        // Drop FKs
        $this->db->query("ALTER TABLE `kesiswaan_prestasi` DROP FOREIGN KEY `fk_prestasi_peserta`");
        $this->db->query("ALTER TABLE `kesiswaan_pelanggaran` DROP FOREIGN KEY `fk_pelanggaran_peserta`");

        // Drop tables
        $this->forge->dropTable('ekstrakurikuler', true);
        $this->forge->dropTable('inventaris_barang', true);
        $this->forge->dropTable('ppdb_pendaftar', true);
        $this->forge->dropTable('kesiswaan_pelanggaran', true);
        $this->forge->dropTable('kesiswaan_prestasi', true);
        $this->forge->dropTable('keuangan_jurnal', true);
    }
}
