<?php

namespace App\Controllers;

use CodeIgniter\Controller;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use Psr\Log\LoggerInterface;

abstract class BaseController extends Controller
{
    protected $helpers = ['auth', 'url'];

    public function initController(RequestInterface $request, ResponseInterface $response, LoggerInterface $logger)
    {
        parent::initController($request, $response, $logger);

        if (session()->get('isLoggedIn')) {
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
            if ($role && $role['permissions']) {
                $permissions = json_decode($role['permissions'], true) ?: [];
            }

            session()->set([
                'role_id' => $user['role_id'],
                'dept_id' => $user['dept_id'],
                'name' => $user['name'],
                'permissions' => $permissions
            ]);

            $notificationModel = new \App\Models\NotificationModel();
            $unreadCount = $notificationModel->where('user_id', $userId)
                ->where('is_read', 0)
                ->countAllResults();

            $this->unreadNotifications = $unreadCount;
            \Config\Services::renderer()->setData(['unreadNotifications' => $unreadCount], 'raw');
        }
    }

    public $unreadNotifications = 0;
}
