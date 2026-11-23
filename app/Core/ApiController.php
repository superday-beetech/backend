<?php

declare(strict_types=1);

namespace App\Core;

use App\Exceptions\DomainException as AppDomainException;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\InvalidArgumentException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Psr\Log\LoggerInterface;
use Throwable;

class ApiController extends ResourceController
{
    protected $format = 'json';

    public function initController(
        RequestInterface $request,
        ResponseInterface $response,
        LoggerInterface $logger
    ) {
        parent::initController($request, $response, $logger);
    }


    /**
     * Mengambil user ID dari Shield.
     */
    protected function getUserId(): int|ResponseInterface
    {
        $userId = (int) auth()->id();

        if ($userId < 1) {
            return $this->errorResponse(
                'User tidak terautentikasi.',
                401
            );
        }

        return $userId;
    }

    // ── Response helpers ─────────────────────

    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200
    ): ResponseInterface {
        return $this->respond([
            'status'  => $code,
            'error'   => false,
            'message' => $message,
            'data'    => $data,
        ], $code);
    }

    protected function errorResponse(
        string|array $message = 'Error',
        int $code = 400,
        mixed $errors = null
    ): ResponseInterface {
        $body = [
            'status'  => $code,
            'error'   => true,
            'message' => $message,
        ];

        if ($errors !== null) {
            $body['errors'] = $errors;
        }

        return $this->respond($body, $code);
    }

    protected function validId($id): ?int
    {
        if (!is_scalar($id) || !ctype_digit((string) $id)) {
            return null;
        }

        $id = (int) $id;

        return $id > 0 ? $id : null;
    }
    // ── Request parsing ──────────────────────

    protected function getRequestData(): ?array
    {
        $contentType = $this->request->getHeaderLine('Content-Type');

        if (str_contains($contentType, 'application/json')) {
            try {
                $data = $this->request->getJSON(true);
                return is_array($data) ? $data : null;
            } catch (Throwable) {
                return null;
            }
        }

        $data = $this->request->getPost();
        return $data !== [] ? $data : null;
    }

    // ── Centralized exception handling ───────

    protected function handleException(
        Throwable $e,
        string $fallback = 'Terjadi kesalahan pada server.'
    ): ResponseInterface {
        if ($e instanceof AppDomainException) {
            return $this->errorResponse(
                $e->getMessage(),
                $e->getStatusCode(),
                $e->getErrors()
            );
        }

        if ($e instanceof InvalidArgumentException) {
            return $this->errorResponse(
                'VALIDATION_ERROR',
                422,
                [$e->getMessage()]
            );
        }

        if ($e instanceof DatabaseException) {
            log_message('error', '[DB] ' . $e->getMessage());
            return $this->errorResponse($fallback, 500);
        }

        log_message('error', '[' . static::class . '] ' . $e->getMessage());
        return $this->errorResponse($fallback, 500);
    }
}
