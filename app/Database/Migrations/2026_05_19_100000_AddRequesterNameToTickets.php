<?php
namespace App\Database\Migrations;
use CodeIgniter\Database\Migration;

class AddRequesterNameToTickets extends Migration
{
    public function up()
    {
        $fields = [
            'requester_name' => [
                'type' => 'VARCHAR',
                'constraint' => '100',
                'null' => true,
                'after' => 'reporter_id'
            ],
        ];
        $this->forge->addColumn('tickets', $fields);
    }

    public function down()
    {
        $this->forge->dropColumn('tickets', 'requester_name');
    }
}
