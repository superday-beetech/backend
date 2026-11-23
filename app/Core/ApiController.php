<?php

declare(strict_types=1);

namespace App\Core;

use App\Enums\ApiErrorCode;
use CodeIgniter\Database\Exceptions\DatabaseException;
use CodeIgniter\Exceptions\InvalidArgumentException as CIInvalidArgumentException;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;
use CodeIgniter\RESTful\ResourceController;
use Modules\Core\Exceptions\DomainException;
use App\Libraries\Pagination\PaginationRequest;
use App\Libraries\Pagination\PaginationResult;
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
     * Mengambil user ID dari CodeIgniter Shield.
     */
    protected function getUserId(): int|ResponseInterface
    {
        $userId = (int) auth()->id();

        if ($userId < 1) {
            return $this->jsonError(
                ApiErrorCode::UNAUTHENTICATED,
                'User tidak terautentikasi.'
            );
        }

        return $userId;
    }

    /**
     * Mengambil parameter pagination dari HTTP Request.
     */
    protected function getPaginationRequest(): PaginationRequest
    {
        return PaginationRequest::fromArray([
            'page'     => $this->request->getGet('page'),
            'per_page' => $this->request->getGet('per_page'),
        ]);
    }

    /**
     * Response sukses standar untuk data berpaginasi.
     *
     * @param array<int, mixed>    $items
     * @param array<string, mixed> $extraMeta
     */
    protected function jsonPaginated(
        array $items,
        PaginationResult $pagination,
        string $message = 'Data berhasil dimuat.',
        int $status = 200,
        array $extraMeta = []
    ): ResponseInterface {
        $meta = array_merge(
            ['pagination' => $pagination->toArray()],
            $extraMeta
        );

        return $this->jsonSuccess(
            data: $items,
            message: $message,
            status: $status,
            meta: $meta
        );
    }

    /**
     * Response sukses standar REST API.
     */
    protected function jsonSuccess(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): ResponseInterface {
        $payload = [
            'status'  => 'success',
            'error'   => false,
            'message' => $message,
            'data'    => $data,
        ];

        if ($meta !== []) {
            $payload['meta'] = $meta;
        }

        return $this->response
            ->setStatusCode($status)
            ->setJSON($payload);
    }

    /**
     * Response error standar REST API.
     */
    protected function jsonError(
        ApiErrorCode|string $code,
        string $message,
        ?int $status = null,
        mixed $errors = null
    ): ResponseInterface {
        $errorCode = $code instanceof ApiErrorCode ? $code->value : $code;
        $httpStatus = $status ?? (
            $code instanceof ApiErrorCode ? $code->defaultHttpStatus() : 400
        );

        $payload = [
            'status'  => 'error',
            'error'   => true,
            'code'    => $errorCode,
            'message' => $message,
            'data'    => null,
        ];

        if ($errors !== null && $errors !== []) {
            $payload['errors'] = $errors;
        }

        return $this->response
            ->setStatusCode($httpStatus)
            ->setJSON($payload);
    }

    /**
     * Kompatibilitas pemanggilan lama successResponse().
     * Meneruskan langsung ke jsonSuccess() agar output JSON tetap seragam.
     */
    protected function successResponse(
        mixed $data = null,
        string $message = 'Success',
        int $code = 200,
        array $meta = []
    ): ResponseInterface {
        return $this->jsonSuccess(
            data: $data,
            message: $message,
            status: $code,
            meta: $meta
        );
    }

    /**
     * Kompatibilitas pemanggilan lama errorResponse().
     * Memetakan HTTP status ke ApiErrorCode dan meneruskan ke jsonError().
     */
    protected function errorResponse(
        string|array $message = 'Error',
        int $code = 400,
        mixed $errors = null,
        ApiErrorCode|string|null $errorCode = null
    ): ResponseInterface {
        $resolvedMessage = is_array($message)
            ? (string) (reset($message) ?: 'Error')
            : $message;

        $resolvedErrors = $errors ?? (is_array($message) ? $message : null);

        $resolvedCode = $errorCode ?? match ($code) {
            400     => ApiErrorCode::BAD_REQUEST,
            401     => ApiErrorCode::UNAUTHENTICATED,
            403     => ApiErrorCode::FORBIDDEN,
            404     => ApiErrorCode::NOT_FOUND,
            409     => ApiErrorCode::CONFLICT,
            410     => ApiErrorCode::GONE,
            422     => ApiErrorCode::VALIDATION_ERROR,
            429     => ApiErrorCode::TOO_MANY_REQUESTS,
            503     => ApiErrorCode::SERVICE_UNAVAILABLE,
            default => $code >= 500
                ? ApiErrorCode::INTERNAL_SERVER_ERROR
                : ApiErrorCode::DOMAIN_ERROR,
        };

        return $this->jsonError(
            code: $resolvedCode,
            message: $resolvedMessage,
            status: $code,
            errors: $resolvedErrors
        );
    }

    /**
     * Validasi ID bilangan bulat positif.
     */
    protected function validId(mixed $id): ?int
    {
        if (!is_int($id) && !is_string($id)) {
            return null;
        }

        if (is_string($id) && ($id === '' || !ctype_digit($id))) {
            return null;
        }

        $validated = filter_var($id, FILTER_VALIDATE_INT, [
            'options' => [
                'min_range' => 1,
            ],
        ]);

        return $validated !== false ? $validated : null;
    }

    /**
     * Mengambil data request berdasarkan Content-Type.
     */
    protected function getRequestData(): ?array
    {
        $contentType = strtolower(
            $this->request->getHeaderLine('Content-Type')
        );

        if (str_contains($contentType, 'application/json')) {
            try {
                $data = $this->request->getJSON(true);

                return is_array($data) ? $data : null;
            } catch (Throwable) {
                return null;
            }
        }

        $method = strtolower($this->request->getMethod());

        if (
            str_contains($contentType, 'application/x-www-form-urlencoded')
            && in_array($method, ['put', 'patch', 'delete'], true)
        ) {
            $data = $this->request->getRawInput();

            return is_array($data) && $data !== [] ? $data : null;
        }

        $data = $this->request->getPost();

        return $data !== [] ? $data : null;
    }

    /**
     * Penanganan exception terpusat.
     */
    protected function handleException(
        Throwable $e,
        string $fallback = 'Terjadi kesalahan pada server.'
    ): ResponseInterface {
        // 1. Exception aturan bisnis / domain aplikasi.
        if ($e instanceof DomainException) {
            return $this->jsonError(
                $e->getErrorCode(),
                $e->getMessage(),
                status: $e->getStatusCode(),
                errors: $e->getErrors()
            );
        }

        // 2. Exception validasi CodeIgniter & PHP global.
        if ($e instanceof CIInvalidArgumentException || $e instanceof \InvalidArgumentException) {
            return $this->jsonError(
                ApiErrorCode::VALIDATION_ERROR,
                $e->getMessage()
            );
        }

        // 3. Kesalahan database.
        if ($e instanceof DatabaseException) {
            log_message(
                'error',
                '[DATABASE ERROR] {message}',
                ['message' => $e->getMessage()]
            );

            return $this->jsonError(
                ApiErrorCode::DATABASE_ERROR,
                $fallback
            );
        }

        // 4. Kesalahan tak terduga.
        log_message(
            'error',
            '[API ERROR] {class}: {message}',
            [
                'class'   => $e::class,
                'message' => $e->getMessage(),
            ]
        );

        return $this->jsonError(
            ApiErrorCode::INTERNAL_SERVER_ERROR,
            $fallback
        );
    }
}
