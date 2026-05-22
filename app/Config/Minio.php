<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class Minio extends BaseConfig
{
    /**
     * MinIO endpoint (host:port).
     */
    public string $endpoint;

    /**
     * Whether to use SSL.
     */
    public bool $useSSL;

    /**
     * MinIO access key.
     */
    public string $accessKey;

    /**
     * MinIO secret key.
     */
    public string $secretKey;

    /**
     * MinIO region (default 'us-east-1' for MinIO).
     */
    public string $region = 'us-east-1';

    /**
     * Bucket name (auto-selected based on environment).
     */
    public string $bucket;

    /**
     * Folder/prefix inside the bucket (auto-selected based on environment).
     */
    public string $folder;

    /**
     * Presigned URL expiry in seconds (default 24 hours).
     */
    public int $presignedExpiry = 86400;

    public function __construct()
    {
        parent::__construct();

        $env = ENVIRONMENT; // 'development' or 'production'

        $this->endpoint = env('MINIO_ENDPOINT', '127.0.0.1:9000');
        $this->useSSL   = filter_var(env('MINIO_USE_SSL', false), FILTER_VALIDATE_BOOLEAN);
        $this->accessKey = env('MINIO_ACCESS_KEY', '');
        $this->secretKey = env('MINIO_SECRET_KEY', '');

        if ($env === 'development') {
            $this->bucket = env('MINIO_BUCKET_DEV', 'devhelpdesk');
            $this->folder = env('MINIO_FOLDER_DEV', 'Documentation');
        } else {
            $this->bucket = env('MINIO_BUCKET_PROD', 'helpdesk');
            $this->folder = env('MINIO_FOLDER_PROD', 'documentation');
        }
    }
}
