<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateSettingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'                  => ['type' => 'INT', 'auto_increment' => true],
            'appname'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama_sekolah'        => ['type' => 'VARCHAR', 'constraint' => 255],
            'npsn'                => ['type' => 'VARCHAR', 'constraint' => 255],
            'nss'                 => ['type' => 'VARCHAR', 'constraint' => 255],
            'jenjang'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'alamat_sekolah'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'website'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'email'               => ['type' => 'VARCHAR', 'constraint' => 255],
            'kelurahan'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'kecamatan'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'kota'                => ['type' => 'VARCHAR', 'constraint' => 255],
            'provinsi'            => ['type' => 'VARCHAR', 'constraint' => 255],
            'nama_kepsek'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'nip_kepsek'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'tahunpelajaran'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'logo'                => ['type' => 'VARCHAR', 'constraint' => 255],
            'logokementrian'      => ['type' => 'VARCHAR', 'constraint' => 255],
            'favicon'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'kop_surat'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'key_encrypt'         => ['type' => 'VARCHAR', 'constraint' => 255],
            'api_token'           => ['type' => 'VARCHAR', 'constraint' => 255],
            'api_url'             => ['type' => 'VARCHAR', 'constraint' => 255],
            'kementrian'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'appversion'          => ['type' => 'VARCHAR', 'constraint' => 255],
            'verifywa_template'   => ['type' => 'TEXT'],
            'updated_at'          => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');
    }

    public function down()
    {
        $this->forge->dropTable('settings');
    }
}
