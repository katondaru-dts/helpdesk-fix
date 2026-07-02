<?php

if (!function_exists('has_permission')) {
    /**
     * Check if the current user has a specific permission.
     *
     * @param string $permission
     * @return bool
     */
    function has_permission(string $permission): bool
    {
        $session = session();

        // Admin (role_id 1) has all permissions
        if ($session->get('role_id') == 1) {
            return true;
        }

        // Combined permissions: User-level + Role permissions
        $userPermissions = $session->get('user_permissions') ?: [];
        $rolePermissions = $session->get('permissions') ?: [];

        $allPermissions = array_unique(array_merge($userPermissions, $rolePermissions));

        return in_array($permission, $allPermissions);
    }
}

if (!function_exists('is_staff')) {
    function is_staff(): bool
    {
        $session = session();
        $val = $session->get('is_staff');
        if ($val === true || $val === 1 || $val === '1') {
            return true;
        }
        return false;
    }
}

if (!function_exists('is_technician')) {
    function is_technician(): bool
    {
        $session = session();
        $val = $session->get('is_technician');
        if ($val === true || $val === 1 || $val === '1') {
            return true;
        }
        return false;
    }
}

if (!function_exists('is_admin')) {
    /**
     * Cek apakah user saat ini adalah administrator (role_id=1).
     */
    function is_admin(): bool
    {
        return session()->get('role_id') == 1;
    }
}

if (!function_exists('validate_password_strength')) {
    /**
     * Memvalidasi kekuatan kata sandi berdasarkan level kekuatan yang dikonfigurasi.
     *
     * @param string $password
     * @param string $strengthLevel (Lemah, Sedang, Kuat)
     * @return string|null Mengembalikan pesan error jika tidak valid, atau null jika valid.
     */
    function validate_password_strength(string $password, string $strengthLevel): ?string
    {
        if ($strengthLevel === 'Lemah') {
            if (strlen($password) < 6) {
                return 'Kata sandi minimal harus 6 karakter.';
            }
        } elseif ($strengthLevel === 'Sedang') {
            if (strlen($password) < 8) {
                return 'Kata sandi minimal harus 8 karakter.';
            }
            if (!preg_match('/[A-Za-z]/', $password) || !preg_match('/[0-9]/', $password)) {
                return 'Kata sandi harus terdiri dari kombinasi huruf dan angka.';
            }
        } elseif ($strengthLevel === 'Kuat') {
            if (strlen($password) < 10) {
                return 'Kata sandi minimal harus 10 karakter.';
            }
            if (!preg_match('/[A-Z]/', $password)) {
                return 'Kata sandi harus mengandung minimal 1 huruf besar.';
            }
            if (!preg_match('/[a-z]/', $password)) {
                return 'Kata sandi harus mengandung minimal 1 huruf kecil.';
            }
            if (!preg_match('/[0-9]/', $password)) {
                return 'Kata sandi harus mengandung minimal 1 angka.';
            }
            if (!preg_match('/[^A-Za-z0-9]/', $password)) {
                return 'Kata sandi harus mengandung minimal 1 simbol atau karakter khusus.';
            }
        }
        return null;
    }
}
