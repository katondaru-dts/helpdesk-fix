<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddRoleUpdatedAtToRoles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('roles', [
            'role_updated_at' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
                'after' => 'is_technician',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('roles', 'role_updated_at');
    }
}
