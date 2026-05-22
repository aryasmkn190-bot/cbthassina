<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateHasilUjianTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'ujian_id'       => ['type' => 'CHAR', 'constraint' => 36, 'null' => false],
            'peserta_id'     => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'guest_id'     => ['type' => 'CHAR', 'constraint' => 36, 'null' => true],
            'waktu_mulai'    => ['type' => 'DATETIME', 'null' => true],
            'waktu_selesai'  => ['type' => 'DATETIME', 'null' => true],
            'nilai_pg'       => ['type' => 'DOUBLE', 'default' => 0],
            'nilai_esai'     => ['type' => 'DOUBLE', 'default' => 0],
            'nilai_total'    => ['type' => 'DOUBLE', 'default' => 0],
            'soal_benar'          => ['type' => 'INT', 'default' => 0],
            'soal_salah'          => ['type' => 'INT', 'default' => 0],
            'kosong'         => ['type' => 'INT', 'default' => 0],
            'poin_benar'          => ['type' => 'INT', 'default' => 0],
            'poin_salah'          => ['type' => 'INT', 'default' => 0],
            'poin_maksimal'          => ['type' => 'INT', 'default' => 0],
            'jawaban_json'  => ['type' => 'TEXT', 'null' => true],
            'urutan_soal' => ['type' => 'JSON', 'null' => true],
            'urutan_opsi' => ['type' => 'JSON', 'null' => true],
            'device_id'       => ['type' => 'VARCHAR', 'constraint' => 255, 'null' => true],
            'token_valid'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'is_device_active'    => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'platform' => [
                'type'       => 'ENUM',
                'constraint' => ['android', 'web', 'desktop'],
                'default'    => 'web',
            ],
            'status'         => [
                'type'       => 'ENUM',
                'constraint' => ['belum_mulai', 'sedang_ujian', 'selesai'],
                'default'    => 'belum_mulai',
            ],

            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->addUniqueKey(['ujian_id', 'peserta_id']); // 1 peserta hanya 1 hasil per ujian
        $this->forge->addKey(['ujian_id']);
        $this->forge->addKey(['peserta_id']);

        $this->forge->addForeignKey('ujian_id', 'ujian', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('peserta_id', 'peserta', 'id', 'CASCADE', 'CASCADE');

        $this->forge->createTable('hasil_ujian');
    }

    public function down()
    {
        $this->forge->dropTable('hasil_ujian');
    }
}
