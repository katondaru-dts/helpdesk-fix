<?php
namespace App\Controllers;
use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\DepartmentModel;

class Profile extends BaseController
{
    public function index()
    {
        $userId = session()->get('id'); // Auth controller menyimpan key 'id'
        $userModel = new UserModel();
        $deptModel = new DepartmentModel();

        $user = $userModel->select('users.*, departments.name as dept_name, roles.name as role_name')
            ->join('departments', 'users.dept_id = departments.id', 'left')
            ->join('roles', 'users.role_id = roles.id', 'left')
            ->find($userId);

        $data = [
            'pageTitle' => 'Profil Saya - Helpdesk',
            'activePage' => 'profile',
            'user' => $user,
            'departments' => $deptModel->findAll()
        ];

        return view('profile/index', $data);
    }

    public function update()
    {
        $userId = session()->get('id');
        $name = $this->request->getPost('name');
        $phone = $this->request->getPost('phone');
        $gender = $this->request->getPost('gender');
        $rules = [
            'name' => 'required|min_length[3]'
        ];

        $updateData = [
            'name' => $name,
            'phone' => $phone,
            'gender' => $gender,
            'notif_sound_enabled' => $this->request->getPost('notif_sound_enabled') ? 1 : 0,
            'notif_sound_type' => $this->request->getPost('notif_sound_type') ?? 'default'
        ];

        if (session()->get('role_id') == 1) {
            $deptId = $this->request->getPost('dept_id');
            $rules['dept_id'] = 'required';
            $updateData['dept_id'] = $deptId;

            $email = $this->request->getPost('email');
            $rules['email'] = "required|valid_email|is_unique[users.email,id,{$userId}]";
            $updateData['email'] = $email;
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', implode(' ', $this->validator->getErrors()));
        }

        $userModel = new UserModel();
        $userModel->update($userId, $updateData);

        $sessionUpdate = [
            'name' => $name,
            'notif_sound_enabled' => $updateData['notif_sound_enabled'],
            'notif_sound_type' => $updateData['notif_sound_type']
        ];
        if (isset($email)) {
            $sessionUpdate['email'] = $email;
        }

        session()->set($sessionUpdate);

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword()
    {
        // Proteksi: user SSO tidak diizinkan ganti password
        if (session()->get('auth_provider') === 'google') {
            return redirect()->to('/profile')->with('error', 'Akun SSO tidak dapat mengganti password melalui aplikasi ini. Silakan kelola password melalui akun Google Anda.');
        }

        $userId = session()->get('id');
        $oldPassword = $this->request->getPost('old_password');
        $newPassword = $this->request->getPost('new_password');
        $confirmPassword = $this->request->getPost('confirm_password');

        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!password_verify($oldPassword, $user['password'])) {
            return redirect()->to('/profile')->with('error', 'Password lama tidak sesuai.');
        }
        if ($newPassword !== $confirmPassword) {
            return redirect()->to('/profile')->with('error', 'Konfirmasi password tidak cocok.');
        }
        if (strlen($newPassword) < 8) {
            return redirect()->to('/profile')->with('error', 'Password baru minimal 8 karakter.');
        }

        $userModel->update($userId, ['password' => password_hash($newPassword, PASSWORD_DEFAULT)]);

        return redirect()->to('/profile')->with('success', 'Password berhasil diubah.');
    }

    public function updateProfilePic()
    {
        $userId = session()->get('id');
        $file = $this->request->getFile('profile_pic');

        if ($file && $file->isValid() && !$file->hasMoved()) {
            $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedTypes)) {
                return redirect()->back()->with('error', 'Format file tidak didukung. Gunakan JPG atau PNG.');
            }

            if ($file->getSize() > 10 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran file terlalu besar. Maksimal 10MB.');
            }

            $minio = new \App\Libraries\MinioStorage();
            $userModel = new UserModel();
            $user = $userModel->find($userId);

            // New format: readable subfolder (e.g. budi_santoso_42/profile.jpg)
            $extension = $file->getExtension();
            $safeName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '_', $user['name']));
            $minioKey = $safeName . '_' . $userId . '/profile.' . $extension;
            $tempName = 'avatar_tmp_' . $userId . '_' . time() . '.' . $extension;
            $tempPath = FCPATH . 'uploads/avatars/' . $tempName;

            // Ensure temp directory exists
            if (!is_dir(FCPATH . 'uploads/avatars')) {
                mkdir(FCPATH . 'uploads/avatars', 0777, true);
            }

            if ($file->move(FCPATH . 'uploads/avatars', $tempName)) {
                try {
                    // Delete old pic from MinIO if exists
                    if (!empty($user['profile_pic']) && is_minio_key($user['profile_pic'])) {
                        $minio->delete($user['profile_pic'], 'avatar');
                    }

                    // Upload to MinIO under per-user subfolder
                    $minio->upload($tempPath, $minioKey, 'avatar');
                    $userModel->update($userId, ['profile_pic' => $minioKey]);
                    session()->set('profile_pic', $minioKey);

                    if (file_exists($tempPath)) {
                        unlink($tempPath);
                    }

                    return redirect()->to('/profile')->with('success', 'Foto profil berhasil diperbarui.');
                } catch (\Exception $e) {
                    return redirect()->back()->with('error', 'Gagal upload ke storage: ' . $e->getMessage());
                }
            }
        }

        return redirect()->back()->with('error', 'Gagal mengunggah foto.');
    }
}
