<?php

if (!function_exists('is_minio_key')) {
    /**
     * Detect whether a stored photo value is a MinIO key (just filename) or a local path.
     */
    function is_minio_key(string $value): bool
    {
        return $value !== '' && !str_contains($value, '/');
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
