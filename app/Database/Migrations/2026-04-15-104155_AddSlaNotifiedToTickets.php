<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlaNotifiedToTickets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tickets', [
            'sla_notified' => [
                'type' => 'TINYINT',
                'constraint' => 1,
                'default' => 0,
                'after' => 'sla_deadline'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', 'sla_notified');
    }
}
