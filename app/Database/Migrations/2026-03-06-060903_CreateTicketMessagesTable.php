<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketMessagesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ticket_id' => ['type' => 'VARCHAR', 'constraint' => '20'],
            'sender_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'message' => ['type' => 'TEXT'],
            'is_internal' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0],
            'sent_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->addForeignKey('sender_id', 'users', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('ticket_messages');
    }

    public function down()
    {
        $this->forge->dropTable('ticket_messages');
    }
}
