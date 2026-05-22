<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddRoleFlagsToRoles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('roles', [
            'is_staff' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'permissions'],
            'is_technician' => ['type' => 'TINYINT', 'constraint' => 1, 'default' => 0, 'after' => 'is_staff'],
        ]);

        // Set default roles
        $db = \Config\Database::connect();
        $db->table('roles')->where('id', 1)->update(['is_staff' => 1]); // Admin
        $db->table('roles')->where('id', 2)->update(['is_staff' => 1, 'is_technician' => 1]); // Support
        $db->table('roles')->where('id', 3)->update(['is_staff' => 0, 'is_technician' => 0]); // User
        $db->table('roles')->where('id', 4)->update(['is_staff' => 1]); // Operator
    }

    public function down()
    {
        $this->forge->dropColumn('roles', 'is_staff');
        $this->forge->dropColumn('roles', 'is_technician');
    }
}
