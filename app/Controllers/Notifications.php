<?php
namespace App\Controllers;
use App\Controllers\BaseController;

class Notifications extends BaseController
{
    public function index()
    {
        $userId = session()->get('id');
        $filter = $this->request->getGet('filter'); // 'unread', 'read', or null (all)

        $notificationModel = new \App\Models\NotificationModel();
        $builder = $notificationModel->select('notifications.*, tickets.title as ticket_title')
            ->join('tickets', 'notifications.ref_id = tickets.id', 'left')
            ->where('notifications.user_id', $userId);

        if ($filter === 'unread') {
            $builder->where('notifications.is_read', 0);
        }
        elseif ($filter === 'read') {
            $builder->where('notifications.is_read', 1);
        }

        $notifications = $builder->orderBy('notifications.created_at', 'DESC')
            ->paginate(20);

        return view('notifications/index', [
            'pageTitle' => 'Notifikasi - Helpdesk',
            'activePage' => 'notifications',
            'notifications' => $notifications,
            'pager' => $notificationModel->pager,
            'currentFilter' => $filter ?: 'all'
        ]);
    }

    public function bulkMarkRead()
    {
        $userId = session()->get('id');
        $ids = $this->request->getPost('ids');

        if (!empty($ids) && is_array($ids)) {
            $db = \Config\Database::connect();
            $db->table('notifications')
                ->whereIn('id', $ids)
                ->where('user_id', $userId)
                ->update(['is_read' => 1]);

            return $this->response->setJSON(['status' => 'success', 'message' => count($ids) . ' notifikasi ditandai telah dibaca.']);
        }

        return $this->response->setJSON(['status' => 'error', 'message' => 'Tidak ada notifikasi yang dipilih.'], 400);
    }

    public function markAllAsRead()
    {
        $userId = session()->get('id');
        $db = \Config\Database::connect();
        $db->table('notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);

        return redirect()->back()->with('success', 'Semua notifikasi telah ditandai dibaca.');
    }

    public function markRead($id = null)
    {
        $userId = session()->get('id');
        $roleId = session()->get('role_id');
        if ($id) {
            $db = \Config\Database::connect();

            // Baca notifikasi SEBELUM update agar data ref_id tersedia
            $notif = $db->query("SELECT * FROM notifications WHERE id = ?", [$id])->getRow();

            // Tandai sebagai terbaca
            if ($roleId == 1 || $roleId == 4) {
                $db->query("UPDATE notifications SET is_read = 1 WHERE id = ?", [$id]);
            }
            else {
                $db->query("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, $userId]);
            }

            // Redirect ke tiket jika ref_id ada DAN tiket masih ada di database
            if ($notif && $notif->ref_id) {
                $ticketExists = $db->query("SELECT id FROM tickets WHERE id = ?", [$notif->ref_id])->getRow();
                if ($ticketExists) {
                    return redirect()->to(base_url('tickets/detail/' . $notif->ref_id));
                }
                else {
                    // Tiket sudah dihapus — redirect ke daftar notifikasi dengan pesan
                    return redirect()->to(base_url('notifications'))->with('error', 'Tiket terkait notifikasi ini sudah tidak tersedia (mungkin telah dihapus).');
                }
            }
        }
        return redirect()->to(base_url('notifications'));
    }

    public function getUnreadCount()
    {
        $userId = session()->get('id');

        // IMPORTANT: Release session lock immediately so other requests are not blocked
        session_write_close();

        if (!$userId) {
            return $this->response->setJSON(['count' => 0, 'latest' => null]);
        }

        $db = \Config\Database::connect();
        $row = $db->query(
            "SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND is_read = 0",
        [$userId]
        )->getRow();

        $latest = $db->query("
            SELECT n.message, t.title as ticket_title
            FROM notifications n
            LEFT JOIN tickets t ON n.ref_id = t.id
            WHERE n.user_id = ? AND n.is_read = 0
            ORDER BY n.created_at DESC LIMIT 1
        ", [$userId])->getRow();

        return $this->response->setJSON([
            'count' => (int)($row->c ?? 0),
            'latest' => $latest ? [
                'message' => $latest->message,
                'title' => $latest->ticket_title ?? 'Notifikasi Baru',
            ] : null,
        ]);
    }
}
