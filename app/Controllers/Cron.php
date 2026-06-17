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
    public function processQueue()
    {
        // Hindari timeout
        set_time_limit(0);
        ignore_user_abort(true);

        $queueModel = new \App\Models\NotificationQueueModel();
        
        // Ambil pending jobs (limit 5 agar tidak kepanjangan tiap eksekusi)
        $jobs = $queueModel->where('status', 'pending')
                           ->orderBy('created_at', 'ASC')
                           ->limit(5)
                           ->findAll();

        if (empty($jobs)) {
            return "No pending jobs.\n";
        }

        helper('email');
        helper('telegram');

        foreach ($jobs as $job) {
            // Tandai processing
            $queueModel->update($job['id'], [
                'status' => 'processing', 
                'attempts' => $job['attempts'] + 1,
                'updated_at' => date('Y-m-d H:i:s')
            ]);

            $payload = json_decode($job['payload'], true);
            $success = false;

            try {
                if ($job['type'] === 'email') {
                    $success = send_email_notification(
                        $payload['to_email'],
                        $payload['to_name'],
                        $payload['subject'],
                        $payload['body']
                    );
                } elseif ($job['type'] === 'telegram') {
                    $success = send_telegram(
                        $payload['message'],
                        $payload['chat_id'] ?? null
                    );
                }
            } catch (\Exception $e) {
                log_message('error', '[Queue] Error processing job ID ' . $job['id'] . ': ' . $e->getMessage());
                $success = false;
            }

            if ($success) {
                $queueModel->delete($job['id']);
            } else {
                $newStatus = ($job['attempts'] + 1) >= 3 ? 'failed' : 'pending';
                $queueModel->update($job['id'], [
                    'status' => $newStatus,
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
        }

        // Jika masih ada sisa, trigger worker lagi
        $remaining = $queueModel->where('status', 'pending')->countAllResults();
        if ($remaining > 0) {
            helper('queue');
            trigger_queue_worker();
        }

        return "Processed " . count($jobs) . " jobs.\n";
    }
}
