<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }
        return view('auth/login');
    }

    public function attemptLogin()
    {
        $userModel = new UserModel();
        $email = $this->request->getPost('email');
        $password = (string)$this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        if ($user && password_verify($password, $user['password'])) {
            if (!$user['is_active']) {
                return redirect()->back()->with('error', 'Akun Anda dinonaktifkan.');
            }

            $this->setUserSession($user);
            return redirect()->to('/dashboard');
        }

        return redirect()->back()->with('error', 'Email atau Password salah.');
    }

    public function setUserSession($user)
    {
        $roleModel = new \App\Models\RoleModel();
        $role = $roleModel->find($user['role_id']);
        $permissions = [];
        if ($role && $role['permissions']) {
            $permissions = json_decode($role['permissions'], true) ?: [];
        }

        $data = [
            'id' => $user['id'],
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'dept_id' => $user['dept_id'],
            'permissions' => $permissions,
            'isLoggedIn' => true,
        ];

        session()->set($data);
        return true;
    }

    public function register()
    {
        $deptModel = new \App\Models\DepartmentModel();
        $data['departments'] = $deptModel->where('is_active', 1)->findAll();
        return view('auth/register', $data);
    }

    public function attemptRegister()
    {
        $userModel = new UserModel();

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => 'required|min_length[1]',
            'dept_id' => 'required',
            'gender' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel->insert([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash((string)$this->request->getPost('password'), PASSWORD_DEFAULT),
            'dept_id' => $this->request->getPost('dept_id'),
            'gender' => $this->request->getPost('gender'),
            'role_id' => 3, // Default User
            'is_active' => 1,
        ]);

        return redirect()->to('/login')->with('success', 'Registrasi berhasil. Silakan login.');
    }

    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login');
    }

    public function googleLogin()
    {
        $client = new \Google\Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));
        $client->addScope("email");
        $client->addScope("profile");

        return redirect()->to($client->createAuthUrl());
    }

    public function googleCallback()
    {
        $client = new \Google\Client();
        $client->setClientId(env('GOOGLE_CLIENT_ID'));
        $client->setClientSecret(env('GOOGLE_CLIENT_SECRET'));
        $client->setRedirectUri(env('GOOGLE_REDIRECT_URI'));

        $code = $this->request->getVar('code');
        if (!$code) {
            return redirect()->to('/login')->with('error', 'Gagal mendapatkan data dari Google.');
        }

        try {
            $token = $client->fetchAccessTokenWithAuthCode($code);
            $client->setAccessToken($token);

            $googleService = new \Google\Service\Oauth2($client);
            $googleUser = $googleService->userinfo->get();

            $email = $googleUser->email;
            $name = $googleUser->name;
            $allowedDomain = env('ALLOWED_EMAIL_DOMAIN', 'unmer.ac.id');

            // Cek domain email
            if (!str_ends_with($email, "@" . $allowedDomain)) {
                return redirect()->to('/login')->with('error', 'Hanya email @' . $allowedDomain . ' yang diizinkan.');
            }

            $userModel = new UserModel();
            $user = $userModel->where('email', $email)->first();

            if (!$user) {
                // User baru, otomatis register sebagai Role 3 (User)
                // Default Departemen (opsional, bisa diset ke NULL atau ID tertentu)
                $userModel->insert([
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), // Random password
                    'role_id' => 3, // Default User
                    'dept_id' => null, // Set null jika departemen belum dipilih
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ]);
                $user = $userModel->where('email', $email)->first();
            }

            if (!$user['is_active']) {
                return redirect()->to('/login')->with('error', 'Akun Anda dinonaktifkan.');
            }

            $this->setUserSession($user);
            return redirect()->to('/dashboard');

        }
        catch (\Exception $e) {
            return redirect()->to('/login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}
