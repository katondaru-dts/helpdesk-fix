<?php
namespace App\Filters;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class RoleCheckFilter implements FilterInterface
{
    public function before(RequestInterface $request, $arguments = null)
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $roleId = $session->get('role_id');
        $sessionRoleUpdatedAt = $session->get('role_updated_at');

        $db = \Config\Database::connect();
        $role = $db->table('roles')->select('role_updated_at')->where('id', $roleId)->get()->getRowArray();

        if (!$role) {
            return;
        }

        $dbRoleUpdatedAt = $role['role_updated_at'];

        // Jika role pernah diupdate dan session belum sinkron, refresh session
        if ($dbRoleUpdatedAt && $dbRoleUpdatedAt !== $sessionRoleUpdatedAt) {
            $userModel = new \App\Models\UserModel();
            $user = $userModel->find($session->get('user_id') ?? $session->get('id'));
            if ($user) {
                $auth = new \App\Controllers\Auth();
                $auth->setUserSession($user);
                $session->set('role_updated_at', $dbRoleUpdatedAt);
            }
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
