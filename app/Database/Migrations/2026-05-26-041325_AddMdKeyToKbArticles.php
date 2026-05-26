<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddMdKeyToKbArticles extends Migration
{
    public function up()
    {
        $this->forge->addColumn('kb_articles', [
            'md_key' => [
                'type' => 'VARCHAR',
                'constraint' => 255,
                'null' => true,
                'after' => 'slug',
            ],
        ]);
    }

    public function down()
    {
        $this->forge->dropColumn('kb_articles', 'md_key');
    }
}
