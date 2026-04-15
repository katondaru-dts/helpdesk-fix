<?php

namespace App\Commands;

use CodeIgniter\CLI\BaseCommand;
use CodeIgniter\CLI\CLI;
use App\Models\TicketModel;
use App\Models\UserModel;

class CheckSlaCommand extends BaseCommand
{
    protected $group = 'Helpdesk';
    protected $name = 'cron:check-sla';
    protected $description = 'Memeriksa tiket yang melanggar SLA dan mengirim notifikasi ke Admin & Operator.';
    protected $usage = 'cron:check-sla';

    public function run(array $params)
    {
        $ticketModel = new TicketModel();
        $userModel = new UserModel();

        CLI::write('Memulai pengecekan SLA...', 'yellow');

        // 1. Cari tiket Overdue yang belum dinotifikasi
        $overdueTickets = $ticketModel
            ->where('sla_deadline <=', date('Y-m-d H:i:s'))
            ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
            ->where('sla_notified', 0)
            ->findAll();

        if (empty($overdueTickets)) {
            CLI::write('Tidak ada tiket baru yang melanggar SLA.', 'green');
            return;
        }

        helper('notification');

        // 2. Ambil daftar Admin (1) & Operator (4)
        $staffToNotify = $userModel
            ->whereIn('role_id', [1, 4])
            ->where('is_active', 1)
            ->findAll();

        $count = 0;
        foreach ($overdueTickets as $ticket) {
            CLI::write('Memproses tiket Overdue: #' . $ticket['id'], 'cyan');

            foreach ($staffToNotify as $staff) {
                add_notification(
                    $staff['id'],
                    'SLA_BREACH',
                    '⚠ Pelanggaran SLA',
                    'Tiket #' . $ticket['id'] . ' "' . $ticket['title'] . '" TELAH OVERDUE!',
                    $ticket['id']
                );
            }

            // Tandai sudah dinotifikasi
            $ticketModel->update($ticket['id'], ['sla_notified' => 1]);
            $count++;
        }

        CLI::write("Selesai! Berhasil mengirim notifikasi untuk $count tiket Overdue ke " . count($staffToNotify) . " staff.", 'green');
    }
}
