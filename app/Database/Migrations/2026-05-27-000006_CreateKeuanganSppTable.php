<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateKeuanganSppTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'             => ['type' => 'CHAR', 'constraint' => 36],
            'peserta_id'     => ['type' => 'CHAR', 'constraint' => 36],
            'bulan'          => ['type' => 'VARCHAR', 'constraint' => 20],
            'nominal'        => ['type' => 'INT', 'constraint' => 11],
            'status_bayar'   => ['type' => 'VARCHAR', 'constraint' => 20],
            'metode_bayar'   => ['type' => 'VARCHAR', 'constraint' => 50, 'null' => true],
            'tanggal_bayar'  => ['type' => 'DATETIME', 'null' => true],
            'invoice_number' => ['type' => 'VARCHAR', 'constraint' => 50, 'unique' => true],
            'created_at'     => ['type' => 'DATETIME', 'null' => true],
            'updated_at'     => ['type' => 'DATETIME', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addKey('peserta_id');
        $this->forge->createTable('keuangan_spp');

        $this->db->query("
            ALTER TABLE `keuangan_spp`
            ADD CONSTRAINT `fk_spp_peserta`
                FOREIGN KEY (`peserta_id`) REFERENCES `peserta` (`id`)
                ON UPDATE CASCADE
                ON DELETE CASCADE
        ");
    }

    public function down()
    {
        $this->db->query("ALTER TABLE `keuangan_spp` DROP FOREIGN KEY `fk_spp_peserta`");
        $this->forge->dropTable('keuangan_spp');
    }
}
