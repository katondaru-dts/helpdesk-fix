<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketHistoryTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ticket_id' => ['type' => 'VARCHAR', 'constraint' => '20'],
            'status' => ['type' => 'VARCHAR', 'constraint' => '50'],
            'notes' => ['type' => 'TEXT', 'null' => true],
            'changed_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'changed_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->addForeignKey('changed_by', 'users', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('ticket_history');
    }

    public function down()
    {
        $this->forge->dropTable('ticket_history');
    }
}
