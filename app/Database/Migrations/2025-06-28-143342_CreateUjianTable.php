<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUjianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                => ['type' => 'CHAR', 'constraint' => 36],
            'bank_soal_id'      => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'jenis_ujian_id'    => ['type' => 'CHAR', 'constraint' => 36],
            'nama_ujian'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'deskripsi'         => ['type' => 'TEXT', 'null' => true],
            'kode_ujian'        => ['type' => 'VARCHAR', 'constraint' => 50],
            'token'             => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'acak_soal'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'acak_opsi'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'pakai_token'       => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'durasi_ujian'      => ['type' => 'INT', 'null' => true],
            'minimal_durasi'    => ['type' => 'INT', 'null' => true],
            'tampil_nilai'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'tampil_pembahasan' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'pakai_webcam'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'perangkat_terkunci' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'single_login'      => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_by'        => ['type' => 'CHAR', 'constraint' => 36],
            'waktu_mulai'       => ['type' => 'DATETIME'],
            'waktu_selesai'     => ['type' => 'DATETIME'],
            'is_active'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'dibagikan'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'butuh_login'         => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'created_at'        => ['type' => 'DATETIME', 'null' => true],
            'updated_at'        => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('bank_soal_id', 'bank_soal', 'id', 'RESTRICT', 'CASCADE');
        $this->forge->createTable('ujian');
    }


    public function down()
    {
        $this->forge->dropTable('ujian');
    }
}
