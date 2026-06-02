<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPhotoToTicketMessages extends Migration
{
    public function up()
    {
        $fields = [
            'photo' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'is_internal',
            ],
        ];
        $this->forge->addColumn('ticket_messages', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('ticket_messages', 'photo');
    }
}
