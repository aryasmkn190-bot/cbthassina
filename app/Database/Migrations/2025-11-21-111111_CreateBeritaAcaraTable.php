<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateBeritaAcaraTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],
            'ujian_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => false,
            ],

            'jenis_ujian_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true,
            ],

            'ruang_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true, // wajib null karena SET NULL
            ],
            'sesi_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 36,
                'null'       => true, // wajib null karena SET NULL
            ],

            'tanggal' => [
                'type' => 'DATE',
                'null' => false,
            ],
            'jam_mulai' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'jam_selesai' => [
                'type' => 'TIME',
                'null' => false,
            ],
            'jumlah_peserta_seharusnya' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'jumlah_hadir' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'jumlah_tidak_hadir' => [
                'type'       => 'INT',
                'constraint' => 5,
                'default'    => 0,
            ],
            'peserta_tidak_hadir' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'catatan' => [
                'type' => 'TEXT',
                'null' => true,
            ],
            'proktor_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'proktor_nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'pengawas_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'pengawas_nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'kepala_sekolah_nama' => [
                'type'       => 'VARCHAR',
                'constraint' => 100,
                'null'       => true,
            ],
            'kepala_sekolah_nip' => [
                'type'       => 'VARCHAR',
                'constraint' => 50,
                'null'       => true,
            ],
            'created_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
            'updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->addKey('ujian_id');
        $this->forge->addKey('jenis_ujian_id');
        $this->forge->addKey('ruang_id');
        $this->forge->addKey('sesi_id');

        // ujian tetap cascade
        $this->forge->addForeignKey('ujian_id', 'ujian', 'id', 'CASCADE', 'CASCADE');

        // jenis ujian → kalau dihapus, set null
        $this->forge->addForeignKey(
            'jenis_ujian_id',
            'jenis_ujian',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // ruang → set null kalau dihapus
        $this->forge->addForeignKey(
            'ruang_id',
            'ruang',
            'id',
            'SET NULL',
            'CASCADE'
        );

        // sesi → set null kalau dihapus
        $this->forge->addForeignKey(
            'sesi_id',
            'sesi',
            'id',
            'SET NULL',
            'CASCADE'
        );

        $this->forge->createTable('berita_acara', true);
    }

    public function down()
    {
        $this->forge->dropTable('berita_acara', true);
    }
}
