<?php

declare(strict_types=1);

namespace Config;

use CodeIgniter\Config\BaseConfig;

/**
 * Kebijakan otorisasi/RBAC.
 * Konfigurasi autentikasi (session/token) hanya berada di AuthPolicy.
 */
class Rbac extends BaseConfig
{
    public const AUTHENTICATED = '@authenticated';

    /** Grup yang melewati grant permission setelah status akun divalidasi. */
    public array $bypassGroups = ['superadmin'];

    /** Endpoint publik yang tidak membutuhkan autentikasi maupun permission. */
    public array $publicPatterns = [
        'acn',
        'acn/**',
        'POST api/v1/auth/login',
        'login',
        'login/*',
        'logout',
        'auth/*',
        'health',
        'webhook/**',
    ];

    /** Pemetaan path -> permission untuk route non-standar. */
    public array $overrides = [
        'GET /' => self::AUTHENTICATED,
    ];

    public int $maxPathLength = 2048;
    public int $maxSegments = 12;
}
