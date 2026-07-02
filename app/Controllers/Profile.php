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

        // Jika phone & gender sudah terisi, clear flag profile_incomplete
        if (!empty($updateData['phone']) && !empty($updateData['gender'])) {
            session()->remove('profile_incomplete');
            session()->remove('show_profile_popup');
        }

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

    /**
     * Halaman wajib pengisian profil (nomor telepon & jenis kelamin)
     * Hanya muncul untuk user baru via SSO yang belum pernah mengisi profil.
     */
    public function complete()
    {
        // Jika sudah lengkap dan bukan user baru SSO, redirect ke dashboard
        $userId = session()->get('id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if ($user && !empty($user['phone']) && !empty($user['gender'])) {
            session()->remove('is_new_sso_user');
            return redirect()->to('/dashboard');
        }

        $data = [
            'pageTitle' => 'Lengkapi Profil - Helpdesk',
            'user'      => $user,
        ];

        return view('profile/complete', $data);
    }

    /**
     * Simpan data profil wajib (nomor telepon & jenis kelamin)
     * dari halaman /profile/complete.
     */
    public function saveComplete()
    {
        $userId = session()->get('id');
        $phone  = trim($this->request->getPost('phone'));
        $gender = $this->request->getPost('gender');

        $rules = [
            'phone'  => 'required|min_length[9]|max_length[20]',
            'gender' => 'required|in_list[L,P]',
        ];
        $messages = [
            'phone'  => [
                'required'   => 'Nomor telepon wajib diisi.',
                'min_length' => 'Nomor telepon minimal 9 digit.',
                'max_length' => 'Nomor telepon terlalu panjang.',
            ],
            'gender' => [
                'required' => 'Jenis kelamin wajib dipilih.',
                'in_list'  => 'Pilih jenis kelamin yang valid.',
            ],
        ];

        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $userModel->update($userId, [
            'phone'  => $phone,
            'gender' => $gender,
        ]);

        // Hapus semua flag profil tidak lengkap dari session
        session()->remove('is_new_sso_user');
        session()->remove('profile_incomplete');
        session()->remove('show_profile_popup');

        return redirect()->to('/dashboard')->with('success', 'Profil berhasil dilengkapi. Selamat datang!');
    }

    /**
     * AJAX: Dismiss popup "Lengkapi Profil" untuk sesi ini.
     * Popup tidak akan muncul lagi sampai user login ulang.
     */
    public function dismissPopup()
    {
        session()->remove('show_profile_popup');
        return $this->response->setJSON(['status' => 'ok']);
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
        // Validasi kekuatan kata sandi baru secara dinamis
        $settingModel = new \App\Models\SettingModel();
        $strengthLevel = $settingModel->getSetting('min_password_strength', 'Sedang');
        $passwordError = validate_password_strength($newPassword, $strengthLevel);
        if ($passwordError) {
            return redirect()->to('/profile')->with('error', $passwordError);
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

            if ($file->getSize() > 20 * 1024 * 1024) {
                return redirect()->back()->with('error', 'Ukuran file terlalu besar. Maksimal 20MB.');
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
                // Compress image before upload
                helper('image');
                compress_image($tempPath, $tempPath, 70, 800); // 800px max width for profile pic

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

    }
    public function deleteProfilePic()
    {
        $userId = session()->get('id');
        $userModel = new UserModel();
        $user = $userModel->find($userId);

        if (!empty($user['profile_pic'])) {
            $minio = new \App\Libraries\MinioStorage();
            try {
                // Delete from MinIO
                if (is_minio_key($user['profile_pic'])) {
                    $minio->delete($user['profile_pic'], 'avatar');
                }

                // Update DB & Session
                $userModel->update($userId, ['profile_pic' => null]);
                session()->set('profile_pic', null);

                return redirect()->to('/profile')->with('success', 'Foto profil berhasil dihapus.');
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal menghapus foto dari storage: ' . $e->getMessage());
            }
        }

        return redirect()->back()->with('error', 'Anda tidak memiliki foto profil untuk dihapus.');
    }
}
