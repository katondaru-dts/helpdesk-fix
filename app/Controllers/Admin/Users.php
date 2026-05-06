<?php
namespace App\Controllers\Admin;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\DepartmentModel;
use App\Models\AuditLogModel;

class Users extends BaseController
{
    public function index()
    {
        $userModel = new UserModel();
        $roleModel = new RoleModel();
        $deptModel = new DepartmentModel();

        $f_role = $this->request->getGet('f-role');
        $search = $this->request->getGet('search');

        $query = $userModel->select('users.*, roles.name as role_name, departments.name as dept_name')
            ->join('roles', 'users.role_id = roles.id', 'left')
            ->join('departments', 'users.dept_id = departments.id', 'left');

        if (!empty($f_role)) {
            $query->where('users.role_id', $f_role);
        }
        if (!empty($search)) {
            $query->groupStart()
                ->like('users.name', $search)
                ->orLike('users.email', $search)
                ->groupEnd();
        }

        $users = $query->orderBy('users.name', 'ASC')->findAll();

        $data = [
            'pageTitle' => 'Kelola User - Helpdesk',
            'activePage' => 'user-management',
            'users' => $users,
            'roles' => $roleModel->findAll(),
            'depts' => $deptModel->findAll(),
            'f_role' => $f_role,
            'search' => $search
        ];

        return view('admin/users/index', $data);
    }

    public function save()
    {
        $userModel = new UserModel();
        $auditLog = new AuditLogModel();

        $id = $this->request->getPost('id');
        $name = $this->request->getPost('name');
        $email = $this->request->getPost('email');
        $password = $this->request->getPost('password');
        $role_id = $this->request->getPost('role_id');
        $dept_id = $this->request->getPost('dept_id');
        $gender = $this->request->getPost('gender');
        $phone = $this->request->getPost('phone');

        if ($id) {
            $rules = [
                'name' => 'required|min_length[3]',
                'email' => "required|valid_email|is_unique[users.email,id,{$id}]",
                'role_id' => 'required',
                'dept_id' => 'required'
            ];

            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
            }

            $data = [
                'name' => $name,
                'email' => $email,
                'role_id' => $role_id,
                'dept_id' => $dept_id,
                'gender' => $gender,
                'phone' => $phone
            ];
            if (!empty($password)) {
                $data['password'] = password_hash($password, PASSWORD_DEFAULT);
            }
            $userModel->update($id, $data);
            $auditLog->logAction('UPDATE', 'users', $id, $data);
            return redirect()->to('/admin/users')->with('success', 'User berhasil diperbarui.');
        } else {
            $data = [
                'name' => $name,
                'email' => $email,
                'password' => password_hash($password ?: 'password123', PASSWORD_DEFAULT),
                'role_id' => $role_id,
                'dept_id' => $dept_id,
                'gender' => $gender,
                'phone' => $phone,
                'is_active' => 1
            ];
            $userModel->insert($data);
            $newId = $userModel->getInsertID();
            unset($data['password']);
            $auditLog->logAction('CREATE', 'users', $newId, $data);

            // Kirim Notifikasi ke Telegram
            $roleData = (new \App\Models\RoleModel())->find($role_id);
            $roleName = $roleData ? $roleData['name'] : 'Unknown';
            $msg = "<b>[USER BARU DIDAFTARKAN]</b>\n"
                . "Nama: {$name}\n"
                . "Email: {$email}\n"
                . "Role: {$roleName}\n";
            if (!empty($phone)) {
                $msg .= "No. HP: {$phone}\n";
            }
            send_telegram($msg);

            return redirect()->to('/admin/users')->with('success', 'User spesifik berhasil ditambahkan.');
        }
    }

    public function toggleStatus()
    {
        $id = $this->request->getPost('id');
        $current = $this->request->getPost('current');

        $userModel = new UserModel();
        $auditLog = new AuditLogModel();
        $targetUser = $userModel->find($id);

        if ($id != session()->get('id') && $targetUser && $targetUser['role_id'] != 1) {
            $newStatus = $current == 1 ? 0 : 1;
            $userModel->update($id, ['is_active' => $newStatus]);
            $auditLog->logAction('TOGGLE_STATUS', 'users', $id, ['is_active' => $newStatus]);
            return redirect()->to('/admin/users')->with('success', 'Status user berhasil diubah.');
        } else {
            return redirect()->to('/admin/users')->with('error', 'Akun Administrator dilindungi dan tidak dapat dinonaktifkan!');
        }
    }

    public function delete()
    {
        $id = $this->request->getPost('id');

        if ($id == session()->get('id')) {
            return redirect()->to('/admin/users')->with('error', 'Tidak dapat menghapus akun sendiri!');
        }

        $userModel = new UserModel();
        $auditLog = new AuditLogModel();

        // Cek apakah user yang akan dihapus ada
        $targetUser = $userModel->find($id);
        if (!$targetUser) {
            return redirect()->to('/admin/users')->with('error', 'User tidak ditemukan.');
        }

        // Cek apakah user masih punya tiket aktif (OPEN atau IN_PROGRESS)
        $db = \Config\Database::connect();
        $activeTickets = $db->table('tickets')
            ->where('reporter_id', $id)
            ->whereIn('status', ['OPEN', 'IN_PROGRESS', 'PENDING'])
            ->countAllResults();

        if ($activeTickets > 0) {
            return redirect()->to('/admin/users')->with('error', "Tidak dapat menghapus user karena masih memiliki {$activeTickets} tiket aktif. Selesaikan atau tutup tiket terlebih dahulu.");
        }

        try {
            $db->transStart();

            // Ambil daftar tiket CLOSED/RESOLVED milik user
            $closedTickets = $db->table('tickets')
                ->select('id')
                ->where('reporter_id', $id)
                ->whereIn('status', ['RESOLVED', 'CLOSED'])
                ->get()->getResultArray();

            $closedTicketIds = array_column($closedTickets, 'id');

            // Hapus dependensi tiket RESOLVED/CLOSED
            if (!empty($closedTicketIds)) {
                $db->table('ticket_messages')->whereIn('ticket_id', $closedTicketIds)->delete();
                $db->table('ticket_history')->whereIn('ticket_id', $closedTicketIds)->delete();
                $db->table('tickets')->whereIn('id', $closedTicketIds)->delete();
            }

            // Hapus data personal user
            $db->table('notifications')->where('user_id', $id)->delete();
            $db->table('ticket_messages')->where('sender_id', $id)->delete();
            $db->table('ticket_history')->where('changed_by', $id)->delete();

            $userModel->delete($id);

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \RuntimeException('Transaksi gagal.');
            }

            $auditLog->logAction('DELETE', 'users', $id, ['name' => $targetUser['name']]);
            return redirect()->to('/admin/users')->with('success', 'User "' . esc($targetUser['name']) . '" berhasil dihapus.');

        } catch (\Throwable $e) {
            $db->transRollback();
            log_message('error', '[Users::delete] Gagal hapus user ID=' . $id . ' — ' . $e->getMessage());
            return redirect()->to('/admin/users')->with('error', 'Gagal menghapus user. Pastikan tidak ada data terkait yang menghalangi.');
        }
    }
}
