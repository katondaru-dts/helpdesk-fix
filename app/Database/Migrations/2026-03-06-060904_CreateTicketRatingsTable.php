<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketRatingsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'ticket_id' => ['type' => 'VARCHAR', 'constraint' => '20'],
            'rated_by' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'rating' => ['type' => 'TINYINT', 'constraint' => 1],
            'feedback' => ['type' => 'TEXT', 'null' => true],
            'rated_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->addForeignKey('rated_by', 'users', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->createTable('ticket_ratings');
    }

    public function down()
    {
        $this->forge->dropTable('ticket_ratings');
    }
}
