<?php

namespace App\Controllers;

use App\Models\TicketModel;
use App\Models\UserModel;

class Cron extends BaseController
{
    /**
     * Check for overdue tickets and notify administrators/operators
     * Can be run via CLI: php spark cron:checkSla
     * Or via URL if needed (but secure it)
     */
    public function checkSla()
    {
        $ticketModel = new TicketModel();
        $userModel = new UserModel();

        // 1. Find tickets that are OVERDUE, NOT RESOLVED/CLOSED, and NOT YET NOTIFIED
        $overdueTickets = $ticketModel
            ->where('sla_deadline <=', date('Y-m-d H:i:s'))
            ->whereNotIn('status', ['RESOLVED', 'CLOSED'])
            ->where('sla_notified', 0)
            ->findAll();

        if (empty($overdueTickets)) {
            return "No overdue tickets found.\n";
        }

        helper('notification');

        // 2. Identify personnel to notify (Admins=1, Operators=4)
        $staffToNotify = $userModel
            ->whereIn('role_id', [1, 4])
            ->where('is_active', 1)
            ->findAll();

        $count = 0;
        foreach ($overdueTickets as $ticket) {
            foreach ($staffToNotify as $staff) {
                add_notification(
                    $staff['id'],
                    'SLA_BREACH',
                    'Pelanggaran SLA (Overdue)',
                    'Tiket #' . $ticket['id'] . ' "' . $ticket['title'] . '" telah melewati batas waktu SLA!',
                    $ticket['id']
                );
            }

            // 3. Mark as notified so we don't spam
            $ticketModel->update($ticket['id'], ['sla_notified' => 1]);
            $count++;
        }

        return "Successfully notified " . count($staffToNotify) . " staff for $count overdue tickets.\n";
    }
}
