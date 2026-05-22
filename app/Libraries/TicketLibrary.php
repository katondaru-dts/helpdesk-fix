<?php

namespace App\Helpers;

use App\Models\TicketModel;
use App\Models\UserModel;
use App\Models\TicketHistoryModel;
use App\Models\TicketMessageModel;

class TicketLibrary
{
    /**
     * Send notifications for ticket events
     */
    public static function notify($ticketId, $type, $senderId, $extraData = [])
    {
        $ticketModel = new TicketModel();
        $userModel = new UserModel();
        $ticket = $ticketModel->getTicketDetail($ticketId);

        if (!$ticket)
            return;

        helper(['notification', 'telegram', 'email']);
        $senderName = session()->get('name') ?: 'System';
        $locationStr = !empty($ticket['location']) ? ' | Lokasi: ' . $ticket['location'] : '';

        $userIdsToNotify = [];

        switch ($type) {
            case 'NEW_TICKET':
                // Notify creator
                add_notification($senderId, 'NEW_TICKET', 'Tiket Berhasil Dibuat', 'Tiket Anda "' . $ticket['title'] . '" telah berhasil dibuat.', $ticketId);

                // Notify staff
                $staff = $userModel->select('users.*')
                    ->join('roles', 'users.role_id = roles.id')
                    ->where('roles.is_staff', 1)
                    ->where('users.is_active', 1)
                    ->findAll();
                foreach ($staff as $s) {
                    if ($s['id'] != $senderId) {
                        add_notification($s['id'], 'NEW_TICKET', 'Tiket Baru Masuk', 'Pengirim: ' . $senderName . $locationStr . ' | Judul: "' . $ticket['title'] . '"', $ticketId);
                    }
                }
                break;

            case 'NEW_MESSAGE':
                $isInternal = $extraData['is_internal'] ?? false;
                $message = $extraData['message'] ?? '';

                if (!$isInternal) {
                    if ($senderId != $ticket['reporter_id']) {
                        $userIdsToNotify[] = $ticket['reporter_id'];
                    }
                }

                if ($ticket['assigned_to'] && $senderId != $ticket['assigned_to']) {
                    $userIdsToNotify[] = $ticket['assigned_to'];
                }

                $admins = $userModel->select('users.*')
                    ->join('roles', 'users.role_id = roles.id')
                    ->where('roles.is_staff', 1)
                    ->where('users.is_active', 1)
                    ->findAll();
                foreach ($admins as $admin) {
                    if ($admin['id'] != $senderId)
                        $userIdsToNotify[] = $admin['id'];
                }

                $userIdsToNotify = array_unique($userIdsToNotify);
                foreach ($userIdsToNotify as $uid) {
                    add_notification($uid, 'NEW_MESSAGE', $isInternal ? 'Pesan Internal Baru' : 'Balasan Pesan Baru', 'Pengirim: ' . $senderName . $locationStr . ' | Tiket: "' . $ticket['title'] . '"', $ticketId);
                }
                break;

            case 'STATUS_CHANGE':
                $newStatus = $extraData['status'] ?? $ticket['status'];
                $notes = $extraData['notes'] ?? '';

                if ($ticket['reporter_id'] && $ticket['reporter_id'] != $senderId) {
                    add_notification($ticket['reporter_id'], 'STATUS_CHANGE', 'Status Tiket Diperbarui', 'Status berubah menjadi: ' . $newStatus . $locationStr, $ticketId);
                }
                break;
        }
    }
}
