<?php

namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class AddEmailTokenToTickets extends Migration
{
    public function up()
    {
        // Tambah kolom email_token ke tabel tickets
        if (!$this->db->fieldExists('email_token', 'tickets')) {
            $this->forge->addColumn('tickets', [
                'email_token' => [
                    'type'       => 'VARCHAR',
                    'constraint' => 64,
                    'null'       => true,
                    'default'    => null,
                    'after'      => 'id',
                ],
            ]);
            // Tambahkan unique index untuk email_token
            $this->db->query('ALTER TABLE `tickets` ADD UNIQUE INDEX `idx_email_token` (`email_token`)');
        }

        // Tambah kolom source ke tabel ticket_messages
        if (!$this->db->fieldExists('source', 'ticket_messages')) {
            $this->forge->addColumn('ticket_messages', [
                'source' => [
                    'type'       => 'ENUM',
                    'constraint' => ['web', 'email'],
                    'null'       => false,
                    'default'    => 'web',
                    'after'      => 'sent_at',
                ],
            ]);
        }
    }

    public function down()
    {
        // Hapus kolom email_token dari tickets
        if ($this->db->fieldExists('email_token', 'tickets')) {
            $this->db->query('ALTER TABLE `tickets` DROP INDEX `idx_email_token`');
            $this->forge->dropColumn('tickets', 'email_token');
        }

        // Hapus kolom source dari ticket_messages
        if ($this->db->fieldExists('source', 'ticket_messages')) {
            $this->forge->dropColumn('ticket_messages', 'source');
        }
    }
}
