<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddLoginSecurityToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'notif_sound_enabled' => [
                'type'       => 'TINYINT',
                'constraint' => 1,
                'default'    => 1,
                'after'      => 'is_active',
            ],
            'notif_sound_type' => [
                'type'       => 'VARCHAR',
                'constraint' => '50',
                'default'    => 'default',
                'after'      => 'notif_sound_enabled',
            ],
            'login_attempts' => [
                'type'       => 'INT',
                'constraint' => 11,
                'default'    => 0,
                'after'      => 'notif_sound_type',
            ],
            'lockout_time' => [
                'type'    => 'DATETIME',
                'null'    => true,
                'after'   => 'login_attempts',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', ['notif_sound_enabled', 'notif_sound_type', 'login_attempts', 'lockout_time']);
    }
}
