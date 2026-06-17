<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketAssigneesTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => [
                'type'           => 'INT',
                'constraint'     => 11,
                'auto_increment' => true,
            ],
            'ticket_id' => [
                'type'       => 'VARCHAR',
                'constraint' => 20,   // sesuai tipe tickets.id (varchar 20)
                'null'       => false,
            ],
            'user_id' => [
                'type'       => 'INT',
                'constraint' => 11,   // sesuai tipe users.id (int 11, bukan unsigned)
                'null'       => false,
            ],
            'assigned_by' => [
                'type'       => 'INT',
                'constraint' => 11,
                'null'       => true,
                'default'    => null,
            ],
            'assigned_at' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'default' => null,
            ],
        ]);

        $this->forge->addPrimaryKey('id');
        $this->forge->addUniqueKey(['ticket_id', 'user_id'], 'unique_assignee');
        $this->forge->addForeignKey('ticket_id', 'tickets', 'id', 'CASCADE', 'CASCADE');
        $this->forge->addForeignKey('user_id', 'users', 'id', 'CASCADE', 'CASCADE');

        // Dynamically fetch the collation of tickets table to avoid mix of collations exception
        $db = \Config\Database::connect();
        $statusQuery = $db->query("SHOW TABLE STATUS LIKE 'tickets'");
        $row = $statusQuery->getRowArray();
        $collation = $row['Collation'] ?? 'utf8mb4_general_ci';

        $attributes = [
            'ENGINE'  => 'InnoDB',
            'CHARSET' => 'utf8mb4',
            'COLLATE' => $collation
        ];

        $this->forge->createTable('ticket_assignees', true, $attributes);
    }

    public function down()
    {
        $this->forge->dropTable('ticket_assignees', true);
    }
}
