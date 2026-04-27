<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\AuditLogModel;

class Roles extends BaseController
{
    private $availablePermissions = [
        'Full Access' => 'Akses Penuh Sistem',
        'Kelola User' => 'Manajemen Pengguna',
        'Kelola Departemen' => 'Manajemen Departemen',
        'Kelola Kategori' => 'Manajemen Kategori',
        'Kelola Role & Izin' => 'Pengaturan Hak Akses',
        'Lihat Laporan' => 'Akses Laporan & Statistik',
        'Ekspor Data' => 'Ekspor Data ke PDF/Excel',
        'Update Status Tiket' => 'Mengubah Status Tiket',
        'Tugaskan Support' => 'Menugaskan Tiket ke Staff Support',
        'Tambah Solusi' => 'Memberikan Solusi Tiket',
        'Buat Tiket' => 'Membuat Tiket Baru',
        'Lihat Tiket Sendiri' => 'Melihat Daftar Tiket Pribadi'
    ];

    public function index()
    {
        $db = \Config\Database::connect();
        $roles = $db->query("
            SELECT r.*, (SELECT COUNT(*) FROM users u WHERE u.role_id = r.id) as user_count 
            FROM roles r ORDER BY r.id ASC
        ")->getResultArray();

        $data = [
            'pageTitle' => 'Kelola Role & Izin',
            'activePage' => 'role-management',
            'roles' => $roles,
            'availablePermissions' => $this->availablePermissions
        ];

        return view('admin/roles/index', $data);
    }

    public function save()
    {
        $roleModel = new RoleModel();
        $auditLog = new AuditLogModel();
        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $code = $this->request->getPost('code');
        $perms = $this->request->getPost('permissions');
        $permissions = $perms ? json_encode(array_values($perms)) : json_encode([]);

        if ($id && $id == 1) {
            return redirect()->to('/admin/roles')->with('error', 'Role Superadmin tidak dapat diubah izinnya.');
        }

        if ($id) {
            $data = [
                'code' => $code,
                'name' => $name,
                'permissions' => $permissions
            ];
            $roleModel->update($id, $data);
            $auditLog->logAction('UPDATE', 'roles', $id, $data);
            return redirect()->to('/admin/roles')->with('success', 'Role diperbarui.');
        }
        else {
            $data = ['code' => $code, 'name' => $name, 'permissions' => $permissions];
            $roleModel->insert($data);
            $auditLog->logAction('CREATE', 'roles', $roleModel->getInsertID(), $data);
            return redirect()->to('/admin/roles')->with('success', 'Role ditambahkan.');
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');
        if ($id == 1) {
            return redirect()->to('/admin/roles')->with('error', 'Role Superadmin dilindungi sistem!');
        }
        $db = \Config\Database::connect();
        $count = $db->query("SELECT COUNT(*) as c FROM users WHERE role_id = ?", [$id])->getRow()->c;

        if ($count == 0) {
            $roleModel = new RoleModel();
            $auditLog = new AuditLogModel();
            $roleModel->delete($id);
            $auditLog->logAction('DELETE', 'roles', $id);
            return redirect()->to('/admin/roles')->with('success', 'Role dihapus.');
        }
        return redirect()->to('/admin/roles')->with('error', 'Membatalkan karena role sedang dipakai user.');
    }
}
