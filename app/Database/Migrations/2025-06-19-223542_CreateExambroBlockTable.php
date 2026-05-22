<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateExambroBlockTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id'           => [
                'type'           => 'INT',
                'unsigned'       => true,
                'auto_increment' => true,
            ],
            'package_name' => [
                'type'       => 'VARCHAR',
                'constraint' => '255',
            ],
            'app_name'     => [
                'type'       => 'VARCHAR',
                'constraint' => '100',
                'null'       => true,
            ],
            'category'     => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'null'       => true,
            ],
            'is_blocked'   => [
                'type'       => 'BOOLEAN',
                'default'    => true,
            ],
            'created_at'   => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
            'updated_at'   => [
                'type'       => 'DATETIME',
                'null'       => true,
            ],
        ]);

        $this->forge->addKey('id', true);
        $this->forge->createTable('exambro_block');
    }

    public function down()
    {
        $this->forge->dropTable('exambro_block');
    }
}
