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
            'gender' => $gender
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

        $sessionUpdate = ['name' => $name];
        if (isset($email)) {
            $sessionUpdate['email'] = $email;
        }

        session()->set($sessionUpdate);

        return redirect()->to('/profile')->with('success', 'Profil berhasil diperbarui.');
    }

    public function changePassword()
    {
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
}
