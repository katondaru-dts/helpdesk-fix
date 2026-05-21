<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhoto2ToTickets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tickets', [
            'photo2' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
                'after' => 'photo'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', 'photo2');
    }
}
