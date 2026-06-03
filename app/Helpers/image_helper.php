<?php

if (!function_exists('compress_image')) {
    /**
     * Compresses an image to a specific quality and optionally resizes it.
     * 
     * @param string $sourcePath Full path to the source image
     * @param string|null $targetPath Full path to save the compressed image (optional, overwrites source if null)
     * @param int $quality Quality from 0 to 100
     * @param int|null $maxWidth Maximum width (null for no resize)
     * @return bool
     */
    function compress_image(string $sourcePath, string $targetPath = null, int $quality = 70, int $maxWidth = 1200): bool
    {
        if ($targetPath === null) {
            $targetPath = $sourcePath;
        }

        try {
            $image = \Config\Services::image()
                ->withFile($sourcePath);

            // Auto-rotate based on EXIF (handles mobile phone camera orientation)
            $image->reorient();

            $width = $image->getWidth();

            // Resize if width is larger than maxWidth
            if ($maxWidth && $width > $maxWidth) {
                // CI4 resize() maintains aspect ratio if one dimension is 0
                $image->resize($maxWidth, 0, true);
            }

            // Save with quality
            return $image->save($targetPath, $quality);
        } catch (\Throwable $e) {
            log_message('error', '[ImageHelper] Compression failed: ' . $e->getMessage());
            return false;
        }
    }
}
