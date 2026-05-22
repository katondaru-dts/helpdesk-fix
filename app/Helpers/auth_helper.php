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

        // User-level permissions override role permissions if set
        $userPermissions = $session->get('user_permissions');
        if (is_array($userPermissions) && !empty($userPermissions)) {
            return in_array($permission, $userPermissions);
        }

        // Fall back to role permissions
        $permissions = $session->get('permissions') ?: [];
        return in_array($permission, $permissions);
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
