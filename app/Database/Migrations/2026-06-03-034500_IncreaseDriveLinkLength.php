<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class IncreaseDriveLinkLength extends Migration
{
    public function up()
    {
        // Mengubah drive_link menjadi TEXT agar bisa menampung URL MinIO yang sangat panjang
        $this->forge->modifyColumn('tickets', [
            'drive_link' => [
                'type' => 'TEXT',
                'null' => true,
            ],
        ]);
    }

    public function down()
    {
        $this->forge->modifyColumn('tickets', [
            'drive_link' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
            ],
        ]);
    }
}
