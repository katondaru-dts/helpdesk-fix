<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddPermissionsToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'permissions' => [
                'type'    => 'TEXT',
                'null'    => true,
                'default' => null,
                'after'   => 'role_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'permissions');
    }
}
