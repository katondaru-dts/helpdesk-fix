<?php

if (!function_exists('is_minio_key')) {
    /**
     * Detect whether a stored photo value is a MinIO key.
     */
    function is_minio_key(string $value): bool
    {
        if ($value === '')
            return false;
        if (!str_contains($value, '/'))
            return true;

        $knownFolders = ['Documentation', 'foto balasan tiket', 'avatar', 'artikel'];
        foreach ($knownFolders as $folder) {
            if (str_starts_with($value, $folder . '/'))
                return true;
        }

        if (str_contains($value, '/profile.'))
            return true;
        return false;
    }
}

if (!function_exists('resolve_minio_url')) {
    /**
     * Resolve a MinIO key into a valid presigned URL.
     */
    function resolve_minio_url(string $value, ?string $defaultFolder = null): ?string
    {
        if (empty($value))
            return null;
        $minio = new \App\Libraries\MinioStorage();

        if (str_contains($value, '/')) {
            $knownFolders = ['Documentation', 'foto balasan tiket', 'avatar', 'artikel'];
            foreach ($knownFolders as $folder) {
                if (str_starts_with($value, $folder . '/')) {
                    $filename = substr($value, strlen($folder) + 1);
                    return $minio->getPresignedUrl($filename, $folder);
                }
            }
            return $minio->getPresignedUrl($value, $defaultFolder);
        }

        $folder = $defaultFolder;
        if (!$folder) {
            if (str_starts_with($value, 'msg_')) {
                $folder = 'foto balasan tiket';
            } elseif (str_starts_with($value, 'avatar_')) {
                $folder = 'avatar';
            } elseif (str_starts_with($value, 'photo_') || str_starts_with($value, 'photo2_')) {
                $folder = $minio->getFolder();
            }
        }

        return $minio->getPresignedUrl($value, $folder);
    }
}

if (!function_exists('get_profile_pic_url')) {
    function get_profile_pic_url($pic): string
    {
        if (empty($pic))
            return base_url('images/default-avatar.png');
        if (is_minio_key($pic)) {
            return resolve_minio_url($pic, 'avatar') ?? base_url('images/default-avatar.png');
        }
        return base_url($pic);
    }
}
