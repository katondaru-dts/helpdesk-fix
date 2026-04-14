<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MainSeeder extends Seeder
{
    public function run()
    {
        // Disable foreign key checks to allow truncation
        $this->db->query('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Roles
        $this->db->table('roles')->truncate();
        $roles = [
            ['id' => 1, 'code' => 'ADMIN', 'name' => 'Administrator', 'permissions' => '["Full Access", "Kelola User/Dept/Cat", "Lihat Semua Laporan"]'],
            ['id' => 2, 'code' => 'STAFF', 'name' => 'IT Support', 'permissions' => '["Update Status Tiket", "Tambah Solusi", "Lihat Laporan"]'],
            ['id' => 3, 'code' => 'USER', 'name' => 'User', 'permissions' => '["Buat Tiket", "Lihat Tiket Sendiri", "Beri Rating"]'],
        ];
        $this->db->table('roles')->insertBatch($roles);

        // 2. Departments
        $this->db->table('departments')->truncate();
        $depts = [
            ['id' => 1, 'name' => 'IT Operations', 'code' => 'IT'],
            ['id' => 2, 'name' => 'Human Resources', 'code' => 'HR'],
            ['id' => 3, 'name' => 'Finance', 'code' => 'FIN'],
            ['id' => 4, 'name' => 'Marketing', 'code' => 'MKT'],
            ['id' => 5, 'name' => 'Security', 'code' => 'SEC'],
        ];
        $this->db->table('departments')->insertBatch($depts);

        // 3. Categories
        $this->db->table('categories')->truncate();
        $cats = [
            ['id' => 1, 'name' => 'Network / Internet'],
            ['id' => 2, 'name' => 'Hardware / PC'],
            ['id' => 3, 'name' => 'Software / Application'],
            ['id' => 4, 'name' => 'Email / Account'],
            ['id' => 5, 'name' => 'Printer / Scanner'],
        ];
        $this->db->table('categories')->insertBatch($cats);

        // 4. Users
        $this->db->table('users')->truncate();
        $users = [
            [
                'name' => 'Admin Utama',
                'email' => 'admin@helpdesk.id',
                'password' => password_hash('admin123', PASSWORD_DEFAULT),
                'role_id' => 1,
                'dept_id' => 1,
                'gender' => 'L',
                'is_active' => 1,
            ],
            [
                'name' => 'Budi Support',
                'email' => 'budi@helpdesk.id',
                'password' => password_hash('support123', PASSWORD_DEFAULT),
                'role_id' => 2,
                'dept_id' => 1,
                'gender' => 'L',
                'is_active' => 1,
            ],
            [
                'name' => 'Katon User',
                'email' => 'katon@helpdesk.id',
                'password' => password_hash('user123', PASSWORD_DEFAULT),
                'role_id' => 3,
                'dept_id' => 2,
                'gender' => 'L',
                'is_active' => 1,
            ],
        ];
        $this->db->table('users')->insertBatch($users);

        // Re-enable foreign key checks
        $this->db->query('SET FOREIGN_KEY_CHECKS=1;');
    }
}
