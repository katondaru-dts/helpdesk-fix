<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddDriveLinkToTickets extends Migration
{
    public function up()
    {
        $fields = [
            'drive_link' => [
                'type' => 'VARCHAR',
                'constraint' => '255',
                'null' => true,
                'after' => 'description'
            ],
        ];
        $this->forge->addColumn('tickets', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', 'drive_link');
    }
}
