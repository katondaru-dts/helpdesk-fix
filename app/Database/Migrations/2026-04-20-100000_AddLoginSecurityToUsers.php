<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLoginSecurityToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'login_attempts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
            ],
            'lockout_time' => [
                'type'    => 'DATETIME',
                'null'    => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['login_attempts', 'lockout_time']);
    }
}
