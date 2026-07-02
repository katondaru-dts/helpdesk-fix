<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use CodeIgniter\HTTP\ResponseInterface;

class Auth extends BaseController
{
    protected $helpers = ['auth', 'url', 'captcha', 'telegram'];
    public function login()
    {
        if (session()->get('isLoggedIn')) {
            return redirect()->to('/dashboard');
        }

        $data = [];

        if (session()->getFlashdata('error')) {
            $data['error'] = session()->getFlashdata('error');
        }

        // Jika CAPTCHA diperlukan, generate soal baru
        if (session()->get('captcha_required')) {
            $data['captcha_required'] = true;
            $data['captcha_question'] = session()->get('captcha_question') ?? generate_captcha();
        }

        return view('auth/login', $data);
    }

    public function attemptLogin()
    {
        $userModel = new UserModel();
        $email = $this->request->getPost('email');
        $password = (string) $this->request->getPost('password');

        $user = $userModel->where('email', $email)->first();

        // ── 0. CEK EMAIL ────────────────────────────────────────────
        if (!$user) {
            return redirect()->back()->with('error', 'Email atau Password salah.')->withInput();
        }

        // ── 1. CEK STATUS AKTIF ─────────────────────────────────────
        if (!$user['is_active']) {
            clear_captcha();
            return redirect()->back()->with('error', 'Akun Anda dinonaktifkan.');
        }

        // ── 2. CEK LOCKOUT ──────────────────────────────────────────
        $lockoutTime = $user['lockout_time'] ?? null;
        $loginAttempts = (int) ($user['login_attempts'] ?? 0);

        if (!empty($lockoutTime) && strtotime($lockoutTime) > time()) {
            // Masih terkunci
            $remainingTime = strtotime($lockoutTime) - time();
            $minutes = floor($remainingTime / 60);
            $seconds = $remainingTime % 60;
            clear_captcha();
            return redirect()->back()->with(
                'error',
                "Akun Anda terkunci. Silakan coba lagi dalam {$minutes} menit {$seconds} detik."
            );
        }

        // Lockout kedaluwarsa → reset siklus
        if (!empty($lockoutTime) && strtotime($lockoutTime) <= time()) {
            $loginAttempts = 0;
            $userModel->update($user['id'], ['login_attempts' => 0, 'lockout_time' => null]);
            clear_captcha();
        }

        // ── 3. VALIDASI CAPTCHA (jika diperlukan) ───────────────────
        if (session()->get('captcha_required')) {
            $userInput = (string) $this->request->getPost('captcha_answer');
            if (!verify_captcha($userInput)) {
                // CAPTCHA salah → generate soal baru, jangan increment attempt
                generate_captcha();
                return redirect()->back()
                    ->with('error', 'Jawaban CAPTCHA salah. Silakan coba lagi.')
                    ->withInput();
            }
            // CAPTCHA benar → hapus dari session (akan di-generate ulang jika masih gagal)
            session()->remove('captcha_answer');
            session()->remove('captcha_question');
        }

        // ── 4. VERIFIKASI PASSWORD ──────────────────────────────────
        if (password_verify($password, $user['password'])) {
            // ✅ LOGIN BERHASIL
            $userModel->update($user['id'], ['login_attempts' => 0, 'lockout_time' => null]);
            clear_captcha();
            $this->setUserSession($user);

            // Cek kelengkapan profil (phone & gender) — hanya untuk role User (role_id=3)
            if (($user['role_id'] ?? 0) == 3) {
                $profileIncomplete = empty($user['phone']) || empty($user['gender']);
                if ($profileIncomplete) {
                    session()->set('profile_incomplete', true);
                }
            }

            return redirect()->to('/dashboard');
        }

        // ❌ PASSWORD SALAH — increment counter
        $newAttempts = $loginAttempts + 1;

        $settingModel   = new \App\Models\SettingModel();
        $maxAttempts    = (int) ($settingModel->getSetting('max_failed_attempts', '5'));
        $lockoutMinutes = (int) ($settingModel->getSetting('lockout_duration', '10'));

        if ($newAttempts >= $maxAttempts) {
            // Kunci akun selama lockoutMinutes menit, reset counter
            $userModel->update($user['id'], [
                'login_attempts' => 0,
                'lockout_time' => date('Y-m-d H:i:s', time() + ($lockoutMinutes * 60)),
            ]);
            clear_captcha();
            $errorMessage = "Terlalu banyak percobaan gagal. Akun Anda dikunci selama {$lockoutMinutes} menit.";
        } else {
            $userModel->update($user['id'], ['login_attempts' => $newAttempts]);
            $attemptsLeft = $maxAttempts - $newAttempts;
            $errorMessage = "Email atau Password salah. Sisa percobaan: {$attemptsLeft}x";

            // Aktifkan CAPTCHA jika sisa 1 percobaan
            if ($attemptsLeft === 1) {
                session()->set('captcha_required', true);
                generate_captcha();
            }
        }

        return redirect()->back()->with('error', $errorMessage)->withInput();
    }


    public function setUserSession($user)
    {
        $roleModel = new \App\Models\RoleModel();
        $role = $roleModel->find($user['role_id']);
        $permissions = [];
        $isStaff = false;
        $isTechnician = false;
        if ($role) {
            if ($role['permissions']) {
                $permissions = json_decode($role['permissions'], true) ?: [];
            }
            $isStaff = !empty($role['is_staff']);
            $isTechnician = !empty($role['is_technician']);
        }

        $userPermissions = null;
        if (!empty($user['permissions'])) {
            $userPermissions = json_decode($user['permissions'], true) ?: null;
        }

        $data = [
            'id' => $user['id'],
            'user_id' => $user['id'],
            'name' => $user['name'],
            'email' => $user['email'],
            'role_id' => $user['role_id'],
            'dept_id' => $user['dept_id'],
            'permissions' => $permissions,
            'user_permissions' => $userPermissions,
            'is_staff' => $isStaff,
            'is_technician' => $isTechnician,
            'isLoggedIn' => true,
            'notif_sound_enabled' => $user['notif_sound_enabled'] ?? 1,
            'notif_sound_type' => $user['notif_sound_type'] ?? 'default',
            'auth_provider' => $user['auth_provider'] ?? 'manual',
        ];

        session()->set($data);
        session()->regenerate(); // Regenerate session ID to prevent fixation
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
        $settingModel = new \App\Models\SettingModel();
        $strengthLevel = $settingModel->getSetting('min_password_strength', 'Sedang');

        $minLen = 8;
        if ($strengthLevel === 'Lemah') {
            $minLen = 6;
        } elseif ($strengthLevel === 'Kuat') {
            $minLen = 10;
        }

        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email|is_unique[users.email]',
            'password' => "required|min_length[{$minLen}]",
            'dept_id' => 'required',
            'gender' => 'required'
        ];

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        // Validasi kekuatan kata sandi tambahan (huruf/angka/simbol)
        $password = (string) $this->request->getPost('password');
        $passwordError = validate_password_strength($password, $strengthLevel);
        if ($passwordError) {
            return redirect()->back()->withInput()->with('errors', ['password' => $passwordError]);
        }

        $userModel->insert([
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => password_hash((string) $this->request->getPost('password'), PASSWORD_DEFAULT),
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

        // Set HTTP timeout agar tidak hang jika Google lambat/timeout
        $client->setHttpClient(new \GuzzleHttp\Client([
            'connect_timeout' => 5, // max 5 detik untuk koneksi
            'timeout' => 8, // max 8 detik total response
        ]));

        $code = $this->request->getVar('code');
        if (!$code) {
            return redirect()->to('/login')->with('error', 'Gagal mendapatkan data dari Google.');
        }

        try {
            // STEP 1: Tukar authorization code → access token (1 network call ke Google)
            $token = $client->fetchAccessTokenWithAuthCode($code);

            if (isset($token['error'])) {
                return redirect()->to('/login')->with('error', 'OAuth Error: ' . ($token['error_description'] ?? $token['error']));
            }

            // STEP 2: Ambil email & name dari id_token (JWT) — TANPA network call tambahan!
            $idToken = $token['id_token'] ?? null;
            if (!$idToken) {
                return redirect()->to('/login')->with('error', 'Gagal mendapatkan identitas dari Google.');
            }

            $parts = explode('.', $idToken);
            $b64 = strtr($parts[1] ?? '', '-_', '+/');
            $b64 = str_pad($b64, strlen($b64) % 4 ? strlen($b64) + (4 - strlen($b64) % 4) : strlen($b64), '=');
            $payload = json_decode(base64_decode($b64), true);

            $email = $payload['email'] ?? null;
            $name = $payload['name'] ?? ($payload['email'] ?? 'User Google');

            if (!$email) {
                return redirect()->to('/login')->with('error', 'Gagal mendapatkan email dari Google.');
            }

            $allowedDomains = explode(',', env('ALLOWED_EMAIL_DOMAIN', 'unmer.ac.id'));
            $allowedDomains = array_map('trim', $allowedDomains);

            $isAllowed = false;
            foreach ($allowedDomains as $domain) {
                if (str_ends_with($email, '@' . $domain)) {
                    $isAllowed = true;
                    break;
                }
            }

            if (!$isAllowed) {
                return redirect()->to('/login')->with('error', 'Email yang digunakan harus terdaftar sebagai civitas akademika Universitas Merdeka Malang.');
            }

            // STEP 3: Cari user + role dalam 1 JOIN query (sebelumnya 2 query terpisah)
            $db = \Config\Database::connect();
            $user = $db->table('users u')
                ->select('u.*, r.permissions as role_permissions')
                ->join('roles r', 'r.id = u.role_id', 'left')
                ->where('u.email', $email)
                ->get()->getRowArray();

            if (!$user) {
                // Cari dept_id "End User"
                $endUserDept = $db->table('departments')->where('name', 'End User')->get()->getRowArray();
                $endUserDeptId = $endUserDept ? $endUserDept['id'] : null;

                // User baru — INSERT lalu ambil ID langsung dari insertID()
                $db->table('users')->insert([
                    'name' => $name,
                    'email' => $email,
                    'password' => password_hash(uniqid((string) rand(), true), PASSWORD_DEFAULT),
                    'role_id' => 3,
                    'dept_id' => $endUserDeptId,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'auth_provider' => 'google',
                ]);
                $newId = $db->insertID();

                // Notifikasi User Baru Via SSO ke Telegram
                $msg = "<b>[USER BARU VIA SSO GOOGLE]</b>\n"
                    . "Nama: {$name}\n"
                    . "Email: {$email}\n"
                    . "Role: User\n"
                    . "Status: Auto-Register via SSO";
                send_telegram($msg);

                // Bangun array user minimal — tidak perlu SELECT ulang
                $user = [
                    'id' => $newId,
                    'name' => $name,
                    'email' => $email,
                    'role_id' => 3,
                    'dept_id' => $endUserDeptId,
                    'is_active' => 1,
                    'notif_sound_enabled' => 1,
                    'notif_sound_type' => 'default',
                    'role_permissions' => null,
                    'auth_provider' => 'google',
                ];
            }

            if (!$user['is_active']) {
                return redirect()->to('/login')->with('error', 'Akun Anda dinonaktifkan.');
            }

            // STEP 4: Set session — gunakan setUserSession agar konsisten dengan login biasa
            // Pastikan auth_provider = google untuk user SSO yang sudah ada
            if (!isset($user['auth_provider']) || $user['auth_provider'] !== 'google') {
                $user['auth_provider'] = 'google';
            }
            $this->setUserSession($user);

            // Cek kelengkapan profil (phone & gender) — hanya untuk role User (role_id=3)
            // User baru via SSO selalu role_id=3, user lama dicek role_id-nya
            $userRoleId = $user['role_id'] ?? 3;
            if ($userRoleId == 3) {
                $profileIncomplete = empty($user['phone']) || empty($user['gender']);
                if ($profileIncomplete) {
                    if ($isNewUser) {
                        // User baru via SSO: paksa redirect ke halaman pengisian profil
                        session()->set('is_new_sso_user', true);
                    } else {
                        // User lama via SSO: tampilkan popup di setiap halaman
                        session()->set('profile_incomplete', true);
                    }
                }
            }

            return redirect()->to('/dashboard');

        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            return redirect()->to('/login')->with('error', 'Koneksi ke Google timeout. Silakan coba lagi.');
        } catch (\Exception $e) {
            return redirect()->to('/login')->with('error', 'Terjadi kesalahan: ' . $e->getMessage());
        }
    }
}

