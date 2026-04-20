<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;
use CodeIgniter\Session\Handlers\BaseHandler;
use CodeIgniter\Session\Handlers\DatabaseHandler;

class Session extends BaseConfig
{
    /**
     * Use DatabaseHandler so multiple concurrent requests do NOT block each other.
     * File-based sessions use exclusive file locks which serializes requests.
     *
     * @var class-string<BaseHandler>
     */
    public string $driver = DatabaseHandler::class;

    public string $cookieName = 'ci_session';

    // 1 Tahun (Dalam Detik). Menghilangkan Auto-Logout.
    public int $expiration = 31536000;

    // Table name for database session storage
    public string $savePath = 'ci_sessions';

    public bool $matchIP = false;

    public int $timeToUpdate = 300;

    public bool $regenerateDestroy = false;

    // Database group (uses default from .env)
    public ?string $DBGroup = null;

    public int $lockRetryInterval = 100_000;
    public int $lockMaxRetries    = 300;
}
