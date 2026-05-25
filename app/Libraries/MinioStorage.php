<?php

namespace App\Libraries;

use Aws\S3\S3Client;
use Aws\Credentials\Credentials;
use Aws\Exception\AwsException;
use Config\Minio;

class MinioStorage
{
    private S3Client $client;
    private Minio $config;

    public function __construct()
    {
        $this->config = config('Minio');

        $credentials = new Credentials(
            $this->config->accessKey,
            $this->config->secretKey
        );

        $scheme = $this->config->useSSL ? 'https' : 'http';

        $this->client = new S3Client([
            'version' => 'latest',
            'region' => $this->config->region,
            'endpoint' => $scheme . '://' . $this->config->endpoint,
            'use_path_style_endpoint' => true,
            'credentials' => $credentials,
            'http' => [
                'connect_timeout' => 5,
                'timeout' => 30,
            ],
        ]);
    }

    /**
     * Resolve final object key using folder prefix.
     *
     * Supports nested subfolders: e.g., filename = 'user_1/profile.jpg'
     * will resolve to 'avatar/user_1/profile.jpg'.
     */
    private function resolveKey(string $filename, ?string $folder = null): string
    {
        $prefix = $folder ?? $this->config->folder;
        $prefix = trim($prefix, '/');
        $filename = ltrim($filename, '/');

        // If filename already starts with the folder prefix, use as-is
        if (str_starts_with($filename, $prefix . '/')) {
            return $filename;
        }

        // Preserve subfolder structure (e.g. 'user_42/profile.jpg')
        // instead of stripping it with basename()
        return $prefix . '/' . $filename;
    }

    /**
     * Extract the basename from the full key (folder prefix removed).
     */
    public function extractFilename(string $key): string
    {
        $folder = trim($this->config->folder, '/');
        $key = ltrim($key, '/');

        if (str_starts_with($key, $folder . '/')) {
            return substr($key, strlen($folder) + 1);
        }

        return $key;
    }

    /**
     * Get the folder prefix for the current environment.
     */
    public function getFolder(): string
    {
        return $this->config->folder;
    }

    /**
     * Get the bucket name for the current environment.
     */
    public function getBucket(): string
    {
        return $this->config->bucket;
    }

    /**
     * Upload a file to MinIO.
     *
     * @param string $sourcePath Absolute path to the source file on disk.
     * @param string $filename   The filename (without folder prefix) to store as.
     * @param string|null $folder Optional custom folder.
     * @return string The full object key stored in the bucket.
     */
    public function upload(string $sourcePath, string $filename, ?string $folder = null): string
    {
        $key = $this->resolveKey($filename, $folder);

        try {
            // Ensure bucket exists
            if (!$this->bucketExists()) {
                $this->client->createBucket(['Bucket' => $this->config->bucket]);
            }

            $this->client->putObject([
                'Bucket' => $this->config->bucket,
                'Key' => $key,
                'SourceFile' => $sourcePath,
            ]);

            return $key;
        } catch (AwsException $e) {
            log_message('error', '[MinIO] Upload failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Generate a presigned URL for reading a file.
     * Includes fallback logic for plural/singular folder mismatch (documentation <=> documentations).
     *
     * @param string $filename The filename or full key stored in MinIO.
     * @param string|null $folder Optional custom folder.
     * @return string|null Presigned URL or null on failure.
     */
    public function getPresignedUrl(string $filename, ?string $folder = null): ?string
    {
        if (empty($filename)) {
            return null;
        }

        $key = $this->resolveKey($filename, $folder);

        // Fallback logic (only if using default folder)
        if (!$folder && !$this->existsWithKey($key)) {
            $fallbackFolder = null;
            if ($this->config->folder === 'documentations') {
                $fallbackFolder = 'documentation';
            } elseif ($this->config->folder === 'documentation') {
                $fallbackFolder = 'documentations';
            }

            if ($fallbackFolder) {
                $fallbackKey = $fallbackFolder . '/' . basename($filename);
                if ($this->existsWithKey($fallbackKey)) {
                    $key = $fallbackKey;
                }
            }
        }

        try {
            $cmd = $this->client->getCommand('GetObject', [
                'Bucket' => $this->config->bucket,
                'Key' => $key,
            ]);

            $request = $this->client->createPresignedRequest($cmd, '+' . $this->config->presignedExpiry . ' seconds');

            return (string) $request->getUri();
        } catch (AwsException $e) {
            log_message('error', '[MinIO] Presigned URL failed: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Delete a file from MinIO.
     *
     * @param string $filename The filename or full key stored in MinIO.
     * @param string|null $folder Optional custom folder.
     */
    public function delete(string $filename, ?string $folder = null): bool
    {
        if (empty($filename)) {
            return false;
        }

        $key = $this->resolveKey($filename, $folder);

        // Fallback for delete: if not found in primary and no custom folder, try fallback
        if (!$folder && !$this->existsWithKey($key)) {
            $fallbackFolder = ($this->config->folder === 'documentations') ? 'documentation' :
                (($this->config->folder === 'documentation') ? 'documentations' : null);
            if ($fallbackFolder) {
                $fallbackKey = $fallbackFolder . '/' . basename($filename);
                if ($this->existsWithKey($fallbackKey)) {
                    $key = $fallbackKey;
                }
            }
        }

        try {
            $this->client->deleteObject([
                'Bucket' => $this->config->bucket,
                'Key' => $key,
            ]);
            return true;
        } catch (AwsException $e) {
            log_message('error', '[MinIO] Delete failed: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Check if a file exists in MinIO.
     */
    public function exists(string $filename, ?string $folder = null): bool
    {
        if (empty($filename)) {
            return false;
        }

        if ($this->existsWithKey($this->resolveKey($filename, $folder))) {
            return true;
        }

        if ($folder)
            return false;

        // Fallback check
        $fallbackFolder = ($this->config->folder === 'documentations') ? 'documentation' :
            (($this->config->folder === 'documentation') ? 'documentations' : null);
        if ($fallbackFolder) {
            return $this->existsWithKey($fallbackFolder . '/' . basename($filename));
        }

        return false;
    }

    /**
     * Check existence by full key.
     */
    private function existsWithKey(string $key): bool
    {
        try {
            return $this->client->doesObjectExist($this->config->bucket, $key);
        } catch (AwsException $e) {
            return false;
        }
    }

    /**
     * Check if the configured bucket exists.
     */
    public function bucketExists(): bool
    {
        try {
            return $this->client->doesBucketExist($this->config->bucket);
        } catch (AwsException $e) {
            return false;
        }
    }

    /**
     * List all objects in the bucket under the configured folder.
     * Used by the migration command.
     *
     * @return array List of object keys.
     */
    public function listObjects(): array
    {
        $folder = trim($this->config->folder, '/');
        $objects = [];

        try {
            $result = $this->client->listObjects([
                'Bucket' => $this->config->bucket,
                'Prefix' => $folder . '/',
            ]);

            if (isset($result['Contents'])) {
                foreach ($result['Contents'] as $obj) {
                    $objects[] = $obj['Key'];
                }
            }
        } catch (AwsException $e) {
            log_message('error', '[MinIO] List objects failed: ' . $e->getMessage());
        }

        return $objects;
    }

    /**
     * Download an object to a local path. Used by the migration rollback.
     */
    public function download(string $filename, string $destinationPath): bool
    {
        $key = $this->resolveKey($filename);

        try {
            $this->client->getObject([
                'Bucket' => $this->config->bucket,
                'Key' => $key,
                'SaveAs' => $destinationPath,
            ]);
            return true;
        } catch (AwsException $e) {
            log_message('error', '[MinIO] Download failed: ' . $e->getMessage());
            return false;
        }
    }
}
