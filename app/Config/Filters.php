<?php

declare(strict_types=1);

namespace Config;

use App\Filters\ApiAwareCsrfFilter;
use App\Filters\RbacFilter;
use CodeIgniter\Config\Filters as BaseFilters;
use CodeIgniter\Filters\Cors;
use CodeIgniter\Filters\DebugToolbar;
use CodeIgniter\Filters\ForceHTTPS;
use CodeIgniter\Filters\Honeypot;
use CodeIgniter\Filters\InvalidChars;
use CodeIgniter\Filters\PageCache;
use CodeIgniter\Filters\PerformanceMetrics;
use CodeIgniter\Filters\SecureHeaders;

class Filters extends BaseFilters
{
    public array $aliases = [
        'csrf'          => ApiAwareCsrfFilter::class,
        'rbac'          => RbacFilter::class,
        'honeypot'      => Honeypot::class,
        'invalidchars'  => InvalidChars::class,
        'secureheaders' => SecureHeaders::class,
        'cors'          => Cors::class,
        'forcehttps'    => ForceHTTPS::class,
        'pagecache'     => PageCache::class,
        'performance'   => PerformanceMetrics::class,
    ];

    public array $required = [
        'before' => [
            'forcehttps',
            'pagecache',
            'cors',
        ],
        'after' => [
            'pagecache',
            'performance',
            'cors',
        ],
    ];

    public array $globals = [
        'before' => [
            // API session Vue wajib CSRF; bearer-only diverifikasi AuthPolicy.
            'csrf' => ['except' => ['webhook/*']],
        ],
        'after' => [],
    ];

    public array $methods = [];
    public array $filters = [];
}
