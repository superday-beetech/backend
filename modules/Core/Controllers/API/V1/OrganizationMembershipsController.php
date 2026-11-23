<?php

declare(strict_types=1);

namespace Modules\Core\Controllers\API\V1;

use App\Core\ApiController;
use CodeIgniter\HTTP\ResponseInterface;
use InvalidArgumentException;
use Modules\Core\Services\OrganizationMembershipService;
use RuntimeException;
use Throwable;

class OrganizationMembershipsController extends ApiController
{
    private OrganizationMembershipService $membershipService;

    public function __construct()
    {
        $this->membershipService = new OrganizationMembershipService();
    }

    /**
     * GET /organization-memberships?organization_id=1
     */
    public function index(): ResponseInterface
    {
        if (($auth = $this->getUserId()) instanceof ResponseInterface) {
            return $auth;
        }

        $organizationId = $this->validId(
            $this->request->getGet('organization_id')
        );

        if ($organizationId === null) {
            return $this->jsonError(
                'NOT_FOUND',
                'Organization ID wajib diisi.',
                422
            );
        }

        try {
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = min(
                100,
                max(1, (int) ($this->request->getGet('per_page') ?? 10))
            );

            $search = $this->request->getGet('q');
            $status = $this->request->getGet('status');

            $result = $this->membershipService->listForOrganization(
                $organizationId,
                $page,
                $perPage,
                $search !== null ? (string) $search : null,
                $status !== null ? (string) $status : null
            );

            return $this->jsonSuccess(
                $result,
                'Data membership berhasil dimuat.'
            );
        } catch (Throwable $e) {
            return $this->handleMembershipException($e);
        }
    }

    /**
     * GET /organization-memberships/{id}?organization_id=1
     */
    public function show($id = null): ResponseInterface
    {

        if (($auth = $this->getUserId()) instanceof ResponseInterface) {
            return $auth;
        }

        $membershipId = $this->validId($id);
        $organizationId = $this->validId(
            $this->request->getGet('organization_id')
        );

        if ($membershipId === null || $organizationId === null) {
            return $this->jsonError(
                'VALIDATION_ERROR',
                'Membership ID dan Organization ID wajib valid.',
                422
            );
        }

        try {
            $membership = $this->membershipService->findForOrganization(
                $organizationId,
                $membershipId
            );

            if ($membership === null) {
                return $this->jsonError(
                    'NOT_FOUND',
                    'Membership tidak ditemukan.',
                    404
                );
            }

            return $this->jsonSuccess(
                $membership,
                'Detail membership berhasil dimuat.'
            );
        } catch (Throwable $e) {
            return $this->handleMembershipException($e);
        }
    }

    /**
     * POST /organization-memberships
     *
     * Body:
     * {
     *   "organization_id": 1,
     *   "user_id": 5,
     *   "membership_role": "member",
     *   "unit_ids": [1, 2],
     *   "default_unit_id": 1
     * }
     */
    public function create(): ResponseInterface
    {
        if (($auth = $this->getUserId()) instanceof ResponseInterface) {
            return $auth;
        }

        $data = $this->getRequestData();

        if ($data === null) {
            return $this->jsonError(
                'BAD_REQUEST',
                'Body request tidak valid atau kosong.',
                400
            );
        }

        $organizationId = $this->validId(
            $data['organization_id'] ?? null
        );

        if ($organizationId === null) {
            return $this->jsonError(
                'VALIDATION_ERROR',
                'Organization ID wajib diisi.',
                422
            );
        }

        try {
            $membershipId = $this->membershipService->create(
                $organizationId,
                $data
            );

            $membership = $this->membershipService->findForOrganization(
                $organizationId,
                $membershipId
            );

            return $this->jsonSuccess(
                $membership,
                'Membership berhasil dibuat.',
                201
            );
        } catch (Throwable $e) {
            return $this->handleMembershipException($e);
        }
    }

    /**
     * PUT/PATCH /organization-memberships/{id}
     *
     * Body:
     * {
     *   "organization_id": 1,
     *   "membership_role": "manager",
     *   "unit_ids": [1, 2],
     *   "default_unit_id": 2
     * }
     */
    public function update($id = null): ResponseInterface
    {
        if (($auth = $this->getUserId()) instanceof ResponseInterface) {
            return $auth;
        }

        $membershipId = $this->validId($id);
        $data = $this->getRequestData();

        if ($membershipId === null) {
            return $this->jsonError(
                'VALIDATION_ERROR',
                'Membership ID tidak valid.',
                422
            );
        }

        if ($data === null) {
            return $this->jsonError(
                'BAD_REQUEST',
                'Body request tidak valid atau kosong.',
                400
            );
        }

        $organizationId = $this->validId(
            $data['organization_id'] ?? null
        );

        if ($organizationId === null) {
            return $this->jsonError(
                'VALIDATION_ERROR',
                'Organization ID wajib diisi.',
                422
            );
        }

        try {
            $this->membershipService->update(
                $organizationId,
                $membershipId,
                $data
            );

            $membership = $this->membershipService->findForOrganization(
                $organizationId,
                $membershipId
            );

            return $this->jsonSuccess(
                $membership,
                'Membership berhasil diperbarui.'
            );
        } catch (Throwable $e) {
            return $this->handleMembershipException($e);
        }
    }

    /**
     * DELETE /organization-memberships/{id}?organization_id=1
     */
    public function delete($id = null): ResponseInterface
    {
        if (($auth = $this->getUserId()) instanceof ResponseInterface) {
            return $auth;
        }

        $membershipId = $this->validId($id);
        $organizationId = $this->validId(
            $this->request->getGet('organization_id')
        );

        if ($membershipId === null || $organizationId === null) {
            return $this->jsonError(
                'VALIDATION_ERROR',
                'Membership ID dan Organization ID wajib valid.',
                422
            );
        }

        try {
            $this->membershipService->deactivate(
                $organizationId,
                $membershipId
            );

            return $this->jsonSuccess(
                ['id' => $membershipId],
                'Membership berhasil dinonaktifkan.'
            );
        } catch (Throwable $e) {
            return $this->handleMembershipException($e);
        }
    }

    /**
     * GET /organization-memberships/index-users
     */
    public function indexUsers(): ResponseInterface
    {
        if (($auth = $this->getUserId()) instanceof ResponseInterface) {
            return $auth;
        }

        try {
            $page = max(1, (int) ($this->request->getGet('page') ?? 1));
            $perPage = min(
                100,
                max(1, (int) ($this->request->getGet('per_page') ?? 10))
            );

            $search = $this->request->getGet('q');

            $result = $this->membershipService->listUsers(
                $page,
                $perPage,
                $search !== null ? (string) $search : null
            );

            return $this->jsonSuccess(
                $result,
                'Daftar user berhasil dimuat.'
            );
        } catch (Throwable $e) {
            return $this->handleMembershipException($e);
        }
    }

    /**
     * Penanganan exception Membership.
     */
    private function handleMembershipException(
        Throwable $e
    ): ResponseInterface {
        if ($e instanceof InvalidArgumentException) {
            return $this->jsonError(
                'VALIDATION_ERROR',
                $e->getMessage(),
                422
            );
        }

        if ($e instanceof RuntimeException) {
            $message = strtolower($e->getMessage());

            if (str_contains($message, 'tidak ditemukan')) {
                return $this->jsonError(
                    'NOT_FOUND',
                    $e->getMessage(),
                    404
                );
            }

            if (str_contains($message, 'tidak aktif')) {
                return $this->jsonError(
                    'NOT_FOUND',
                    $e->getMessage(),
                    404
                );
            }

            return $this->jsonError(
                'BAD_REQUEST',
                $e->getMessage(),
                422
            );
        }

        return $this->handleException(
            $e,
            'Gagal memproses data membership.'
        );
    }
}
