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
        $permissions = $session->get('permissions') ?: [];

        // Admin (role_id 1) has all permissions
        if ($session->get('role_id') == 1) {
            return true;
        }

        return in_array($permission, $permissions);
    }
}

