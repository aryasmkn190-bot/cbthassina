<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExambroMenuTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'          => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'title'       => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
            ],
            'link'        => [
                'type'       => 'TEXT',
            ],
            'icon'        => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'is_active'   => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'order'       => [
                'type'       => 'INT',
                'default'    => 0,
            ],
            'token'        => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'is_token'   => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'tgl_dibuka'  => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'tgl_ditutup'  => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'created_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at'  => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true); // Primary key
        $this->forge->createTable('exambro_menu');
    }

    public function down()
    {
        $this->forge->dropTable('exambro_menu');
    }
}
