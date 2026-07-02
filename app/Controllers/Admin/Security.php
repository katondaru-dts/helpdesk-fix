<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\SettingModel;
use App\Models\RoleModel;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class Security extends BaseController
{
    public function index()
    {
        $settingModel = new SettingModel();
        $settings = $settingModel->getAllSettings();

        $db = \Config\Database::connect();
        
        // Load Roles
        $roles = $db->query("
            SELECT r.*, (SELECT COUNT(*) FROM users u WHERE u.role_id = r.id) as user_count 
            FROM roles r ORDER BY r.id ASC
        ")->getResultArray();

        // Load Users with Filters (search, f-role)
        $userModel = new UserModel();
        $f_role = $this->request->getGet('f-role');
        $search = $this->request->getGet('search');

        $userQuery = $userModel->select('users.*, roles.name as role_name, departments.name as dept_name')
            ->join('roles', 'users.role_id = roles.id', 'left')
            ->join('departments', 'users.dept_id = departments.id', 'left');

        if (!empty($f_role)) {
            $userQuery->where('users.role_id', $f_role);
        }
        if (!empty($search)) {
            $userQuery->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
        }
        $users = $userQuery->orderBy('users.name', 'ASC')->findAll();

        // Load Departments
        $departments = $db->query("
            SELECT d.id, d.name,
                   COALESCE(d.is_active, 1) as is_active,
                   (SELECT COUNT(*) FROM users u WHERE u.dept_id = d.id) as user_count 
            FROM departments d 
            ORDER BY d.name ASC
        ")->getResultArray();

        $availablePermissions = [
            'Full Access' => 'Akses Penuh Sistem',
            'Kelola User' => 'Manajemen Pengguna',
            'Kelola Departemen' => 'Manajemen Departemen',
            'Kelola Kategori' => 'Manajemen Kategori',
            'Kelola Role & Izin' => 'Pengaturan Hak Akses',
            'Lihat Laporan' => 'Akses Laporan & Statistik',
            'Ekspor Data' => 'Ekspor Data ke PDF/Excel',
            'Cetak Laporan' => 'Mencetak Laporan Tiket',
            'Update Status Tiket' => 'Mengubah Status Tiket',
            'Tugaskan Support' => 'Menugaskan Tiket ke Staff Support',
            'Tambah Solusi' => 'Memberikan Solusi Tiket',
            'Buat Tiket' => 'Membuat Tiket Baru',
            'Lihat Tiket Sendiri' => 'Melihat Daftar Tiket Pribadi'
        ];

        helper('auth');
        $defaultTab = is_admin() ? 'settings' : 'roles';

        // Override default activeTab if specified in GET query params
        $activeTab = $this->request->getGet('activeTab') ?: (session()->getFlashdata('activeTab') ?? $defaultTab);

        $data = [
            'pageTitle'  => 'Akun dan Keamanan',
            'activePage' => 'admin-security',
            'settings'   => $settings,
            'roles'      => $roles,
            'users'      => $users,
            'departments'=> $departments,
            'depts'      => (new DepartmentModel())->findAll(), // for the user modal dropdown
            'f_role'     => $f_role,
            'search'     => $search,
            'availablePermissions' => $availablePermissions,
            'activeTab'  => $activeTab
        ];

        return view('admin/security/index', $data);
    }
}
