<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTicketsTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'VARCHAR', 'constraint' => '20'],
            'title' => ['type' => 'VARCHAR', 'constraint' => '200'],
            'description' => ['type' => 'TEXT', 'null' => true],
            'cat_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'priority' => ['type' => 'ENUM', 'constraint' => ['LOW', 'MEDIUM', 'HIGH', 'URGENT'], 'default' => 'MEDIUM'],
            'reporter_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true],
            'assigned_to' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'dept_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'location' => ['type' => 'VARCHAR', 'constraint' => '100', 'null' => true],
            'status' => ['type' => 'ENUM', 'constraint' => ['OPEN', 'IN_PROGRESS', 'PENDING', 'RESOLVED', 'CLOSED'], 'default' => 'OPEN'],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'updated_at' => ['type' => 'TIMESTAMP', 'null' => true],
            'closed_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('cat_id', 'categories', 'id', 'SET NULL', 'NO ACTION');
        $this->forge->addForeignKey('reporter_id', 'users', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->addForeignKey('assigned_to', 'users', 'id', 'SET NULL', 'NO ACTION');
        $this->forge->addForeignKey('dept_id', 'departments', 'id', 'SET NULL', 'NO ACTION');
        $this->forge->createTable('tickets');
    }

    public function down()
    {
        $this->forge->dropTable('tickets');
    }
}
