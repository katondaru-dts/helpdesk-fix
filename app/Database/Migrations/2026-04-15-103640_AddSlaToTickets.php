<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddSlaToTickets extends Migration
{
    public function up()
    {
        $this->forge->addColumn('tickets', [
            'sla_deadline' => [
                'type' => 'DATETIME',
                'null' => true,
                'after' => 'status'
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', 'sla_deadline');
    }
}
