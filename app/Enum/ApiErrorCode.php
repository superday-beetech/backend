<?php

declare(strict_types=1);

namespace App\Enums;

enum ApiErrorCode: string
{
    // 400 / 422 - Request & Validasi Input
    case INVALID_ID            = 'INVALID_ID';
    case EMPTY_PAYLOAD       = 'EMPTY_PAYLOAD';
    case BAD_REQUEST         = 'BAD_REQUEST';
    case VALIDATION_ERROR    = 'VALIDATION_ERROR';

    // 401 / 403 - Autentikasi & Otorisasi
    case UNAUTHENTICATED     = 'UNAUTHENTICATED';
    case TOKEN_EXPIRED       = 'TOKEN_EXPIRED';
    case FORBIDDEN           = 'FORBIDDEN';

    // 404 / 409 / 410 - Resource & Konflik Data
    case NOT_FOUND           = 'NOT_FOUND';
    case CONFLICT            = 'CONFLICT';
    case GONE                = 'GONE';

    // 422 / 429 - Aturan Bisnis & Rate Limit
    case DOMAIN_ERROR        = 'DOMAIN_ERROR';
    case INSUFFICIENT_STOCK  = 'INSUFFICIENT_STOCK';
    case TOO_MANY_REQUESTS   = 'TOO_MANY_REQUESTS';

    // 500 / 503 - Server & Infrastruktur
    case DATABASE_ERROR        = 'DATABASE_ERROR';
    case INTERNAL_SERVER_ERROR = 'INTERNAL_SERVER_ERROR';
    case SERVICE_UNAVAILABLE = 'SERVICE_UNAVAILABLE';

    /**
     * Mengembalikan HTTP status code bawaan untuk tiap kode error.
     */
    public function defaultHttpStatus(): int
    {
        return match ($this) {
            self::INVALID_ID,
            self::EMPTY_PAYLOAD,
            self::BAD_REQUEST          => 400,

            self::UNAUTHENTICATED,
            self::TOKEN_EXPIRED        => 401,

            self::FORBIDDEN            => 403,
            self::NOT_FOUND            => 404,
            self::CONFLICT             => 409,
            self::GONE                 => 410,

            self::VALIDATION_ERROR,
            self::DOMAIN_ERROR,
            self::INSUFFICIENT_STOCK   => 422,

            self::TOO_MANY_REQUESTS    => 429,

            self::DATABASE_ERROR,
            self::INTERNAL_SERVER_ERROR => 500,

            self::SERVICE_UNAVAILABLE  => 503,
        };
    }
}
