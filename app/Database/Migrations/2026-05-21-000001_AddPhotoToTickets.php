<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToTickets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tickets', [
            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => '200',
                'null' => true,
                'after' => 'drive_link'
            ]
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', 'photo');
    }
}
