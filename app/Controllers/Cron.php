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
            ->whereNotIn('status', ['RESOLVED', 'CLOSED', 'PENDING'])
            ->where('sla_notified', 0)
            ->findAll();

        if (empty($overdueTickets)) {
            return "No overdue tickets found.\n";
        }

        helper('notification');

        // 2. Identify personnel to notify (all staff)
        $staffToNotify = $userModel->select('users.*')
            ->join('roles', 'users.role_id = roles.id')
            ->where('roles.is_staff', 1)
            ->where('users.is_active', 1)
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

    /**
     * Endpoint HTTP untuk memicu fetch email replies secara manual atau via scheduler eksternal.
     * Diamankan dengan CRON_SECRET token (query string atau header X-Cron-Token).
     *
     * URL: GET /cron/fetch-email-replies?token=SECRET
     */
    public function fetchEmailReplies()
    {
        $expectedToken = env('CRON_SECRET', '');

        // Validasi token keamanan
        $providedToken = $this->request->getGet('token')
                      ?? $this->request->getHeaderLine('X-Cron-Token');

        if (empty($expectedToken) || $providedToken !== $expectedToken) {
            return $this->response
                ->setStatusCode(403)
                ->setBody(json_encode(['error' => 'Forbidden: invalid or missing token']))
                ->setContentType('application/json');
        }

        // Jalankan command via CLI service
        $command = service('commands');
        ob_start();
        try {
            $command->run('cron:fetch-email-replies', []);
        } catch (\Throwable $e) {
            ob_end_clean();
            log_message('error', '[Cron::fetchEmailReplies] ' . $e->getMessage());
            return $this->response
                ->setStatusCode(500)
                ->setBody(json_encode(['error' => $e->getMessage()]))
                ->setContentType('application/json');
        }
        $output = ob_get_clean();

        return $this->response
            ->setStatusCode(200)
            ->setBody(json_encode(['status' => 'ok', 'output' => $output]))
            ->setContentType('application/json');
    }
}
