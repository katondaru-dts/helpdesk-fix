<?php
namespace App\Controllers;
use App\Controllers\BaseController;

class Notifications extends BaseController
{
    public function index()
    {
        $session = session();
        $userId = $session->get('id');
        $roleId = $session->get('role_id');
        $db = \Config\Database::connect();

        // Mark all as read for this user
        $db->query("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0", [$userId]);

        // Fetch notifications for current user
        $notifications = $db->query("
            SELECT n.*, t.title as ticket_title
            FROM notifications n
            LEFT JOIN tickets t ON n.ref_id = t.id
            WHERE n.user_id = ?
            ORDER BY n.created_at DESC
            LIMIT 50
        ", [$userId])->getResultArray();

        $data = [
            'pageTitle' => 'Notifikasi - Helpdesk',
            'activePage' => 'notifications',
            'notifications' => $notifications,
        ];

        return view('notifications/index', $data);
    }

    public function allNotifications()
    {
        $session = session();
        $roleId = $session->get('role_id');

        // Only admin (1), IT Support (2) and operator (4) can access
        if ($roleId != 1 && $roleId != 2 && $roleId != 4) {
            return redirect()->to('/notifications')->with('error', 'Akses ditolak.');
        }

        $db = \Config\Database::connect();

        // Fetch all notifications with user info and ticket info
        $notifications = $db->query("
            SELECT n.*, 
                   u.name as target_user_name, 
                   u.email as target_user_email,
                   r.name as target_role_name,
                   t.title as ticket_title
            FROM notifications n
            LEFT JOIN users u ON n.user_id = u.id
            LEFT JOIN roles r ON u.role_id = r.id
            LEFT JOIN tickets t ON n.ref_id = t.id
            ORDER BY n.created_at DESC
            LIMIT 200
        ")->getResultArray();

        $data = [
            'pageTitle' => 'Semua Notifikasi - Helpdesk',
            'activePage' => 'notifications-all',
            'notifications' => $notifications,
        ];

        return view('notifications/admin', $data);
    }

    public function markRead($id = null)
    {
        $userId = session()->get('id');
        $roleId = session()->get('role_id');
        if ($id) {
            $db = \Config\Database::connect();

            // Check if admin/operator to mark any notification, otherwise just user's own
            if ($roleId == 1 || $roleId == 4) {
                $db->query("UPDATE notifications SET is_read = 1 WHERE id = ?", [$id]);
            }
            else {
                $db->query("UPDATE notifications SET is_read = 1 WHERE id = ? AND user_id = ?", [$id, $userId]);
            }

            // Find the notification to potentially redirect
            $notif = $db->query("SELECT * FROM notifications WHERE id = ?", [$id])->getRow();
            if ($notif && $notif->ref_id) {
                return redirect()->to(base_url('tickets/detail/' . $notif->ref_id));
            }
        }
        return redirect()->back();
    }

    public function getUnreadCount()
    {
        $userId = session()->get('id');
        $db = \Config\Database::connect();
        $row = $db->query("SELECT COUNT(*) as c FROM notifications WHERE user_id = ? AND is_read = 0", [$userId])->getRow();
        return $this->response->setJSON(['count' => $row->c ?? 0]);
    }
}
