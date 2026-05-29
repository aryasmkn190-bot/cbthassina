<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddDapodikFieldsAndEditRequest extends Migration
{
    private function getNewFields()
    {
        return [
            'nis' => ['type' => 'VARCHAR', 'constraint' => 30, 'null' => true, 'after' => 'nisn'],
            'nik' => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true, 'after' => 'nis'],
            'tempat_lahir' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true, 'after' => 'nama'],
            'tanggal_lahir' => ['type' => 'DATE', 'null' => true, 'after' => 'tempat_lahir'],
            'jenis_kelamin' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'null' => true, 'after' => 'tanggal_lahir'],
            'telepon' => ['type' => 'VARCHAR', 'constraint' => 20, 'null' => true],
            'email' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'alamat' => ['type' => 'TEXT', 'null' => true],
            'rt' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'rw' => ['type' => 'VARCHAR', 'constraint' => 5, 'null' => true],
            'dusun' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kelurahan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kecamatan' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'kode_pos' => ['type' => 'VARCHAR', 'constraint' => 10, 'null' => true],
            'jenis_tinggal' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'alat_transportasi' => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            
            // Ayah
            'nama_ayah' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nik_ayah' => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'tahun_lahir_ayah' => ['type' => 'INT', 'null' => true],
            'pendidikan_ayah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pekerjaan_ayah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'penghasilan_ayah' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],

            // Ibu
            'nama_ibu' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nik_ibu' => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'tahun_lahir_ibu' => ['type' => 'INT', 'null' => true],
            'pendidikan_ibu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pekerjaan_ibu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'penghasilan_ibu' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],

            // Wali
            'nama_wali' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'nik_wali' => ['type' => 'VARCHAR', 'constraint' => 16, 'null' => true],
            'tahun_lahir_wali' => ['type' => 'INT', 'null' => true],
            'pendidikan_wali' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'pekerjaan_wali' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
            'penghasilan_wali' => ['type' => 'VARCHAR', 'constraint' => 100, 'null' => true],
        ];
    }

    public function up()
    {
        // 1. Add fields to peserta
        $this->forge->addColumn('peserta', $this->getNewFields());

        // 2. Create pengajuan_edit_siswa table
        $this->forge->addField([
            'id'            => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'peserta_id'    => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'data_lama'     => ['type' => 'TEXT', 'null' => false],
            'data_baru'     => ['type' => 'TEXT', 'null' => false],
            'status'        => ['type' => 'ENUM', 'constraint' => ['menunggu', 'disetujui', 'ditolak'], 'default' => 'menunggu'],
            'catatan_admin' => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'created_at'    => ['type' => 'DATETIME', 'null' => true],
            'updated_at'    => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('peserta_id');
        $this->forge->createTable('pengajuan_edit_siswa');

        // Add foreign key
        $this->db->query("ALTER TABLE `pengajuan_edit_siswa` ADD CONSTRAINT `fk_pengajuan_peserta` FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`) ON UPDATE CASCADE ON DELETE CASCADE");
    }

    public function down()
    {
        // Drop foreign key and table
        $this->db->query("ALTER TABLE `pengajuan_edit_siswa` DROP FOREIGN KEY `fk_pengajuan_peserta`");
        $this->forge->dropTable('pengajuan_edit_siswa', true);

        // Remove fields from peserta
        $fields = array_keys($this->getNewFields());
        $this->forge->dropColumn('peserta', $fields);
    }
}
