<?php

declare(strict_types=1);

namespace App\Libraries\DynamicRouter;

use CodeIgniter\HTTP\IncomingRequest;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\Shield\Entities\User;
use Config\AuthPolicy;

class AuthenticationResolver
{
    private const ACCESS_COOKIE = 'acn_access_token';

    private ?User $resolvedUser = null;
    private bool $userResolved = false;

    private AuthPolicy $policy;

    public function __construct(
        private RequestInterface $request,
        ?AuthPolicy $policy = null,
    ) {
        $this->policy = $policy ?? config(AuthPolicy::class);
    }

    public function isApiRequest(): bool
    {
        $path = trim($this->request->getUri()->getPath(), '/');
        $prefix = trim($this->policy->apiPrefix, '/');

        if ($prefix !== '' && ($path === $prefix || str_starts_with($path, $prefix . '/'))) {
            return true;
        }

        // Endpoint non-prefix yang tetap harus JSON (webhook, health, oauth)
        foreach ($this->policy->apiNonPrefixedPaths ?? [] as $exception) {
            $exception = trim($exception, '/');
            if ($exception !== '' && ($path === $exception || str_starts_with($path, $exception . '/'))) {
                return true;
            }
        }

        return false;
    }

    public function isAuthenticated(): bool
    {
        return $this->getAuthenticatedUser() !== null;
    }

    public function getAuthenticatedUser(): ?User
    {
        if ($this->userResolved) {
            return $this->resolvedUser;
        }
        $this->userResolved = true;

        try {
            if ($this->isApiRequest()) {
                return $this->resolvedUser = $this->resolveApiUser();
            }
            return $this->resolvedUser = $this->resolveWebUser();
        } catch (\Throwable $e) {
            log_message('error', 'AuthenticationResolver: gagal resolve user — ' . $e->getMessage());
            return $this->resolvedUser = null;
        }
    }

    private function resolveApiUser(): ?User
    {
        foreach ($this->policy->apiAuthenticators as $authenticator) {
            $user = match (strtolower((string) $authenticator)) {
                'session' => $this->getSessionUser(),
                'tokens', 'bearer' => $this->authenticateToken($this->getBearerToken()),
                default => null,
            };

            if ($user !== null) {
                return $user;
            }
        }

        return null;
    }

    private function resolveWebUser(): ?User
    {
        foreach ($this->policy->webAuthenticators as $authenticator) {
            $user = match (strtolower($authenticator)) {
                'session' => $this->getSessionUser(),
                'cookie'  => $this->authenticateToken(
                    $this->getCookieValue(self::ACCESS_COOKIE)
                ),
                default => null,
            };

            if ($user !== null) {
                return $user;
            }
        }

        return null;
    }

    public function getSessionUser(): ?User
    {
        try {
            $authenticator = auth('session');
            if (! $authenticator->loggedIn()) {
                return null;
            }
            $user = $authenticator->user();
            return $user instanceof User ? $user : null;
        } catch (\Throwable $e) {
            log_message('error', 'AuthenticationResolver: gagal resolve session user — ' . $e->getMessage());
            return null;
        }
    }

    private function authenticateToken(string $rawToken): ?User
    {
        $rawToken = trim($rawToken);

        if ($rawToken === '') {
            return null;
        }

        $authenticator = auth('tokens')->getAuthenticator();

        $result = $authenticator->check([
            'token' => $rawToken,
        ]);

        if (! $result->isOK()) {
            return null;
        }

        $user = $result->extraInfo();

        return $user instanceof User ? $user : null;
    }

    private function getBearerToken(): string
    {
        $header = trim(
            $this->request->getHeaderLine('Authorization')
        );

        if (
            $header === ''
            || ! preg_match('/^Bearer\s+(.+)$/i', $header, $matches)
        ) {
            return '';
        }

        return trim($matches[1]);
    }

    private function getCookieValue(string $name): string
    {
        if (! $this->request instanceof IncomingRequest) {
            return '';
        }

        $value = $this->request->getCookie($name);

        return is_string($value) ? trim($value) : '';
    }
}
