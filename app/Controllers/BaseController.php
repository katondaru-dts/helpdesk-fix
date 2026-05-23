<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['auth', 'url', 'telegram', 'app'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        // Skip heavy session refresh for AJAX/polling requests (e.g. unread-count)
        $isAjax = $request->isAJAX()
            || str_contains($request->getUri()->getPath(), 'unread-count')
            || str_contains($request->getUri()->getPath(), 'ai/chat');

        if (!$isAjax && session()->get('isLoggedIn')) {
            $this->refreshSessionIfNeeded();
        }
    }

    private function refreshSessionIfNeeded()
    {
        $userId = session()->get('id');
        if (!$userId)
            return;

        $userModel = new \App\Models\UserModel();
        $user = $userModel->find($userId);

        if ($user) {
            if (!$user['is_active']) {
                session()->destroy();
                header('Location: ' . base_url('login?error=' . urlencode('Akun Anda dinonaktifkan.')));
                exit;
            }

            $roleModel = new \App\Models\RoleModel();
            $role = $roleModel->find($user['role_id']);
            $permissions = [];
            $isStaff = false;
            $isTechnician = false;
            if ($role) {
                if ($role['permissions']) {
                    $permissions = json_decode($role['permissions'], true) ?: [];
                }
                $isStaff = !empty($role['is_staff']);
                $isTechnician = !empty($role['is_technician']);
            }

            $userPermissions = null;
            if (!empty($user['permissions'])) {
                $userPermissions = json_decode($user['permissions'], true) ?: null;
            }

            session()->set([
                'role_id' => $user['role_id'],
                'dept_id' => $user['dept_id'],
                'name' => $user['name'],
                'permissions' => $permissions,
                'user_permissions' => $userPermissions,
                'is_staff' => $isStaff,
                'is_technician' => $isTechnician,
                'notif_sound_enabled' => $user['notif_sound_enabled'] ?? 1,
                'notif_sound_type' => $user['notif_sound_type'] ?? 'default',
                'profile_pic' => $user['profile_pic'] ?? null,
            ]);

            $notificationModel = new \App\Models\NotificationModel();
            $dbNotif = \Config\Database::connect();
            $builderUnread = $dbNotif->table('notifications n')
                ->join('tickets t', 'n.ref_id = t.id', 'left')
                ->where('n.user_id', $userId)
                ->where('n.is_read', 0);

            // Non-staff hanya hitung notifikasi dari tiket miliknya sendiri
            if (!$isStaff) {
                $builderUnread->where('t.reporter_id', $userId);
            }

            $unreadCount = $builderUnread->countAllResults();

            $this->unreadNotifications = $unreadCount;
            \Config\Services::renderer()->setData(['unreadNotifications' => $unreadCount], 'raw');
        }
    }

    public $unreadNotifications = 0;
}
