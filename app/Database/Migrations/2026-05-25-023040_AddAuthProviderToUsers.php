<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddAuthProviderToUsers extends Migration
{
    public function up()
    {
        $fields = [
            'auth_provider' => [
                'type' => 'VARCHAR',
                'constraint' => 20,
                'null' => false,
                'default' => 'manual',
                'after' => 'is_active',
            ],
        ];

        $this->forge->addColumn('users', $fields);

        // Set auth_provider = 'google' untuk user yang tidak punya password (dibuat via SSO)
        // Password user SSO adalah random hash, bukan null, jadi kita tidak bisa bedakan dari DB.
        // Kolom ini akan diisi dengan benar saat user login berikutnya.
    }

    public function down()
    {
        $this->forge->dropColumn('users', 'auth_provider');
    }
}
