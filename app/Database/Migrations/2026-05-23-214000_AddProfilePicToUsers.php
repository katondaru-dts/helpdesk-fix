<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddProfilePicToUsers extends Migration
{
    public function up()
    {
        $this->forge->addColumn('users', [
            'profile_pic' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'dept_id',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'profile_pic');
    }
}
