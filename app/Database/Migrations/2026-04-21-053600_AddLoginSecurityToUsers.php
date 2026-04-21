<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLoginSecurityToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'login_attempts' => [
                'type' => 'INT',
                'constraint' => 11,
                'null' => false,
                'default' => 0,
                'after' => 'notif_sound_type',
            ],
            'lockout_time' => [
                'type' => 'DATETIME',
                'null' => true,
                'default' => null,
                'after' => 'login_attempts',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['login_attempts', 'lockout_time']);
    }
}
