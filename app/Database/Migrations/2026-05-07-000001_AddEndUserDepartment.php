<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEndUserDepartment extends Migration
{
    public function up()
    {
        $exists = $this->db->table('departments')->where('name', 'End User')->countAllResults();
        if (!$exists) {
            $this->db->table('departments')->insert([
                'name' => 'End User',
                'code' => 'EU',
                'is_active' => 1,
            ]);
        }
    }

    public function down()
    {
        $this->db->table('departments')->where('name', 'End User')->delete();
    }
}
