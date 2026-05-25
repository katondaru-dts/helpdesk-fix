<?php

if (!function_exists('is_minio_key')) {
    /**
     * Detect whether a stored photo value is a MinIO key.
     *
     * Supports two formats:
     *  - Old (flat): 'avatar_42_1716800000.jpg'  → no slash
     *  - New (per-user folder): 'user_42/profile.jpg' → starts with 'user_'
     */
    function is_minio_key(string $value): bool
    {
        if ($value === '')
            return false;

        // Format lama: flat filename (tidak ada slash)
        if (!str_contains($value, '/'))
            return true;

        // Format baru: per-user subfolder (mengandung '/profile.')
        return str_contains($value, '/profile.');
    }
}

if (!function_exists('get_profile_pic_url')) {
    /**
     * Get the resolved URL for a profile picture.
     */
    function get_profile_pic_url($pic): string
    {
        if (empty($pic)) {
            return base_url('images/default-avatar.png');
        }

        if (is_minio_key($pic)) {
            $minio = new \App\Libraries\MinioStorage();
            // Use 'avatar' folder for profile pictures
            return $minio->getPresignedUrl($pic, 'avatar') ?? base_url('images/default-avatar.png');
        }

        return base_url($pic);
    }
}
