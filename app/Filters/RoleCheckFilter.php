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
        $role = $db->table('roles')
            ->select('role_updated_at, permissions, is_staff, is_technician')
            ->where('id', $roleId)
            ->get()
            ->getRowArray();

        if (!$role) {
            return;
        }

        $dbRoleUpdatedAt = $role['role_updated_at'];

        // Jika role pernah diupdate dan session belum sinkron, refresh data session
        // Tanpa memanggil session()->regenerate() untuk menghindari session loop
        if ($dbRoleUpdatedAt && $dbRoleUpdatedAt !== $sessionRoleUpdatedAt) {
            $permissions = [];
            if (!empty($role['permissions'])) {
                $permissions = json_decode($role['permissions'], true) ?: [];
            }

            $session->set([
                'permissions' => $permissions,
                'is_staff' => !empty($role['is_staff']),
                'is_technician' => !empty($role['is_technician']),
                'role_updated_at' => $dbRoleUpdatedAt,
            ]);
        }
    }

    public function after(RequestInterface $request, ResponseInterface $response, $arguments = null)
    {
        //
    }
}
