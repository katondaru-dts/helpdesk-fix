<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateUsersTable extends Migration
{
    public function up()
    {
        $this->forge->addField([
            'id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'auto_increment' => true],
            'name' => ['type' => 'VARCHAR', 'constraint' => '100'],
            'email' => ['type' => 'VARCHAR', 'constraint' => '100', 'unique' => true],
            'password' => ['type' => 'VARCHAR', 'constraint' => '255'],
            'role_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'default' => 3],
            'dept_id' => ['type' => 'INT', 'constraint' => 11, 'unsigned' => true, 'null' => true],
            'gender' => ['type' => 'ENUM', 'constraint' => ['L', 'P'], 'null' => true],
            'phone' => ['type' => 'VARCHAR', 'constraint' => '20', 'null' => true],
            'birth_date' => ['type' => 'DATE', 'null' => true],
            'is_active' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 1],
            'created_at' => ['type' => 'TIMESTAMP', 'null' => true],
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('role_id', 'roles', 'id', 'CASCADE', 'NO ACTION');
        $this->forge->addForeignKey('dept_id', 'departments', 'id', 'SET NULL', 'NO ACTION');
        $this->forge->createTable('users');
    }

    public function down()
    {
        $this->forge->dropTable('users');
    }
}
