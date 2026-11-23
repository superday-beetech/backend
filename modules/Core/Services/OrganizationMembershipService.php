<?php

declare(strict_types=1);

namespace Modules\Core\Services;

use CodeIgniter\Database\BaseConnection;
use Config\Database;
use InvalidArgumentException;
use RuntimeException;
use Throwable;
use Modules\Core\Models\OrganizationModel;
use Modules\Core\Models\OrganizationMembershipModel;

class OrganizationMembershipService
{
    private BaseConnection $db;
    private OrganizationModel $organizations;
    private OrganizationMembershipModel $memberships;

    public function __construct()
    {
        $this->db = Database::connect();
        $this->organizations = new OrganizationModel();
        $this->memberships = new OrganizationMembershipModel();
    }

    public function listUsers(
        int $page = 1,
        int $perPage = 10,
        ?string $search = null
    ): array {
        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $builder = $this->db->table('users')
            ->select('id, username');

        if ($search !== null && trim($search) !== '') {
            $builder->like('username', trim($search));
        }

        $total = $builder->countAllResults(false);

        $items = $builder
            ->orderBy('username', 'ASC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ];
    }

    public function listForOrganization(
        int $organizationId,
        int $page = 1,
        int $perPage = 10,
        ?string $search = null,
        ?string $status = null
    ): array {
        $this->validateOrganization($organizationId);

        $page = max(1, $page);
        $perPage = max(1, min(100, $perPage));

        $builder = $this->db->table('core_organization_memberships m')
            ->select([
                'm.id',
                'm.organization_id',
                'm.user_id',
                'm.membership_role',
                'm.status',
                'm.is_default',
                'm.created_at',
                'm.updated_at',
                'u.username',
            ])
            ->join('users u', 'u.id = m.user_id', 'left')
            ->where('m.organization_id', $organizationId);

        if ($status !== null && $status !== '') {
            $builder->where('m.status', $status);
        }

        if ($search !== null && trim($search) !== '') {
            $keyword = trim($search);

            $builder->groupStart()
                ->like('u.username', $keyword)
                ->orLike('m.membership_role', $keyword)
                ->orLike('m.user_id', $keyword)
                ->groupEnd();
        }

        $total = $builder->countAllResults(false);

        $items = $builder
            ->orderBy('m.is_default', 'DESC')
            ->orderBy('m.id', 'DESC')
            ->limit($perPage, ($page - 1) * $perPage)
            ->get()
            ->getResultArray();

        foreach ($items as &$item) {
            $units = $this->getMembershipUnits(
                (int) $item['id'],
                $organizationId
            );

            $defaultUnit = $this->findDefaultUnit($units);

            $item['units'] = $units;
            $item['unit_ids'] = array_map(
                static fn (array $unit): int => (int) $unit['id'],
                $units
            );

            // Kompatibilitas API lama.
            $item['unit_id'] = $defaultUnit['id']
                ?? ($units[0]['id'] ?? null);

            $item['unit_name'] = $defaultUnit['name']
                ?? ($units[0]['name'] ?? null);
        }
        unset($item);

        return [
            'items' => $items,
            'pagination' => [
                'current_page' => $page,
                'per_page' => $perPage,
                'total' => $total,
                'total_pages' => (int) ceil($total / $perPage),
            ],
        ];
    }

    public function findForOrganization(
        int $organizationId,
        int $membershipId
    ): ?array {
        $this->validateOrganization($organizationId);

        $membership = $this->memberships
            ->where('organization_id', $organizationId)
            ->where('id', $membershipId)
            ->first();

        if (!$membership) {
            return null;
        }

        $units = $this->getMembershipUnits(
            $membershipId,
            $organizationId
        );

        $defaultUnit = $this->findDefaultUnit($units);

        $membership['units'] = $units;

        $membership['unit_ids'] = array_map(
            static fn (array $unit): int => (int) $unit['id'],
            $units
        );

        // unit_id lama selalu mengikuti unit default.
        $membership['unit_id'] = $defaultUnit['id'] ?? null;
        $membership['unit_name'] = $defaultUnit['name'] ?? null;

        return $membership;
    }

    public function create(int $organizationId, array $data): int
    {
        $this->validateOrganization($organizationId);

        $payload = $this->normalizeData($data, true);
        $unitIds = $this->extractUnitIds($data);

        $this->validateUnits($organizationId, $unitIds);

        $existing = $this->memberships
            ->where('organization_id', $organizationId)
            ->where('user_id', $payload['user_id'])
            ->first();

        if ($existing) {
            throw new InvalidArgumentException(
                'User sudah terdaftar dalam organisasi ini.'
            );
        }

        $defaultUnitId = $this->resolveDefaultUnitId(
            $data,
            $unitIds,
            null
        );

        $payload['organization_id'] = $organizationId;

        $this->db->transBegin();

        try {
            $membershipId = $this->memberships->insert($payload);

            if ($membershipId === false) {
                throw new RuntimeException($this->getModelErrors());
            }

            $membershipId = (int) $membershipId;

            $this->syncUnits(
                $membershipId,
                $organizationId,
                $unitIds,
                $defaultUnitId
            );

            // Kolom legacy mengikuti unit default.
            $this->syncLegacyUnitId(
                $membershipId,
                $defaultUnitId
            );

            $this->commitOrFail(
                'Gagal menyimpan membership dan unit.'
            );

            return $membershipId;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function update(
        int $organizationId,
        int $membershipId,
        array $data
    ): bool {
        $this->validateOrganization($organizationId);

        $membership = $this->findForOrganization(
            $organizationId,
            $membershipId
        );

        if (!$membership) {
            throw new RuntimeException('Membership tidak ditemukan.');
        }

        $payload = $this->normalizeData($data, false);

        if (isset($payload['user_id'])) {
            $existing = $this->memberships
                ->where('organization_id', $organizationId)
                ->where('user_id', $payload['user_id'])
                ->where('id !=', $membershipId)
                ->first();

            if ($existing) {
                throw new InvalidArgumentException(
                    'User sudah terdaftar dalam organisasi ini.'
                );
            }
        }

        $hasUnitUpdate = array_key_exists('unit_ids', $data)
            || array_key_exists('unit_id', $data);

        $hasDefaultUpdate = array_key_exists(
            'default_unit_id',
            $data
        );

        $unitIds = $hasUnitUpdate
            ? $this->extractUnitIds($data)
            : array_map(
                static fn (array $unit): int => (int) $unit['id'],
                $membership['units'] ?? []
            );

        $this->validateUnits($organizationId, $unitIds);

        $defaultUnitId = $this->resolveDefaultUnitId(
            $data,
            $unitIds,
            $membership['units'] ?? []
        );

        if (
            $payload === []
            && !$hasUnitUpdate
            && !$hasDefaultUpdate
        ) {
            throw new InvalidArgumentException(
                'Tidak ada data yang diperbarui.'
            );
        }

        $this->db->transBegin();

        try {
            if ($payload !== []) {
                $updated = $this->memberships->update(
                    $membershipId,
                    $payload
                );

                if (!$updated) {
                    throw new RuntimeException($this->getModelErrors());
                }
            }

            if ($hasUnitUpdate || $hasDefaultUpdate) {
                $this->syncUnits(
                    $membershipId,
                    $organizationId,
                    $unitIds,
                    $defaultUnitId
                );

                $this->syncLegacyUnitId(
                    $membershipId,
                    $defaultUnitId
                );
            }

            $this->commitOrFail(
                'Gagal memperbarui membership.'
            );

            return true;
        } catch (Throwable $e) {
            $this->db->transRollback();
            throw $e;
        }
    }

    public function deactivate(
        int $organizationId,
        int $membershipId
    ): bool {
        $this->validateOrganization($organizationId);

        $membership = $this->findForOrganization(
            $organizationId,
            $membershipId
        );

        if (!$membership) {
            throw new RuntimeException('Membership tidak ditemukan.');
        }

        if ($membership['status'] === 'inactive') {
            throw new InvalidArgumentException(
                'Membership sudah tidak aktif.'
            );
        }

        return $this->memberships->update(
            $membershipId,
            ['status' => 'inactive']
        );
    }

    private function normalizeData(
        array $data,
        bool $creating
    ): array {
        $allowedFields = [
            'user_id',
            'membership_role',
            'status',
            'is_default',
        ];

        $payload = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $payload[$field] = $data[$field];
            }
        }

        if ($creating && empty($payload['user_id'])) {
            throw new InvalidArgumentException('User wajib dipilih.');
        }

        if (isset($payload['user_id'])) {
            if (
                !is_numeric($payload['user_id'])
                || (int) $payload['user_id'] <= 0
            ) {
                throw new InvalidArgumentException('User ID tidak valid.');
            }

            $payload['user_id'] = (int) $payload['user_id'];
        }

        if (isset($payload['membership_role'])) {
            $payload['membership_role'] = trim(
                (string) $payload['membership_role']
            );

            if ($payload['membership_role'] === '') {
                throw new InvalidArgumentException(
                    'Role membership tidak boleh kosong.'
                );
            }
        } elseif ($creating) {
            $payload['membership_role'] = 'member';
        }

        if (isset($payload['status'])) {
            if (
                !in_array(
                    $payload['status'],
                    ['active', 'inactive'],
                    true
                )
            ) {
                throw new InvalidArgumentException(
                    'Status membership tidak valid.'
                );
            }
        } elseif ($creating) {
            $payload['status'] = 'active';
        }

        if (array_key_exists('is_default', $payload)) {
            $payload['is_default'] = filter_var(
                $payload['is_default'],
                FILTER_VALIDATE_BOOLEAN
            ) ? 1 : 0;
        } elseif ($creating) {
            $payload['is_default'] = 0;
        }

        return $payload;
    }

    private function extractUnitIds(array $data): array
    {
        if (array_key_exists('unit_ids', $data)) {
            if (!is_array($data['unit_ids'])) {
                throw new InvalidArgumentException(
                    'unit_ids harus berupa array.'
                );
            }

            $values = $data['unit_ids'];
        } elseif (
            array_key_exists('unit_id', $data)
            && $data['unit_id'] !== null
            && $data['unit_id'] !== ''
        ) {
            $values = [$data['unit_id']];
        } else {
            $values = [];
        }

        $ids = [];

        foreach ($values as $value) {
            if (
                !is_numeric($value)
                || (int) $value <= 0
                || (string) (int) $value !== (string) $value
                    && !is_int($value)
            ) {
                throw new InvalidArgumentException(
                    'Terdapat Unit ID yang tidak valid.'
                );
            }

            $ids[] = (int) $value;
        }

        return array_values(array_unique($ids));
    }

    private function validateUnits(
        int $organizationId,
        array $unitIds
    ): void {
        if ($unitIds === []) {
            return;
        }

        $units = $this->db->table('core_organization_units')
            ->where('organization_id', $organizationId)
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->whereIn('id', $unitIds)
            ->get()
            ->getResultArray();

        if (count($units) !== count($unitIds)) {
            throw new InvalidArgumentException(
                'Satu atau lebih unit tidak valid, tidak aktif, atau bukan milik organisasi ini.'
            );
        }
    }

    private function resolveDefaultUnitId(
        array $data,
        array $unitIds,
        ?array $existingUnits
    ): ?int {
        if (array_key_exists('default_unit_id', $data)) {
            $value = $data['default_unit_id'];

            if ($value === null || $value === '') {
                return null;
            }

            if (!is_numeric($value) || (int) $value <= 0) {
                throw new InvalidArgumentException(
                    'Unit default tidak valid.'
                );
            }

            $defaultId = (int) $value;

            if (!in_array($defaultId, $unitIds, true)) {
                throw new InvalidArgumentException(
                    'Unit default harus termasuk dalam daftar unit membership.'
                );
            }

            return $defaultId;
        }

        if (
            array_key_exists('unit_id', $data)
            && !array_key_exists('unit_ids', $data)
        ) {
            return $unitIds[0] ?? null;
        }

        if (count($unitIds) === 1) {
            return $unitIds[0];
        }

        if ($existingUnits !== null) {
            foreach ($existingUnits as $unit) {
                if (
                    (int) ($unit['is_default'] ?? 0) === 1
                    && in_array((int) $unit['id'], $unitIds, true)
                ) {
                    return (int) $unit['id'];
                }
            }
        }

        return null;
    }

    private function syncUnits(
        int $membershipId,
        int $organizationId,
        array $unitIds,
        ?int $defaultUnitId
    ): void {
        $this->validateUnits($organizationId, $unitIds);

        if (
            $defaultUnitId !== null
            && !in_array($defaultUnitId, $unitIds, true)
        ) {
            throw new InvalidArgumentException(
                'Unit default tidak termasuk dalam unit membership.'
            );
        }

        $table = $this->db->table(
            'core_organization_membership_units'
        );

        $table->where('membership_id', $membershipId)->delete();

        if ($unitIds === []) {
            return;
        }

        $now = date('Y-m-d H:i:s');
        $rows = [];

        foreach ($unitIds as $unitId) {
            $rows[] = [
                'membership_id' => $membershipId,
                'organization_unit_id' => $unitId,
                'is_default' => $defaultUnitId === $unitId ? 1 : 0,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        if (!$table->insertBatch($rows)) {
            throw new RuntimeException(
                'Gagal menyimpan relasi unit membership.'
            );
        }
    }

    private function syncLegacyUnitId(
        int $membershipId,
        ?int $defaultUnitId
    ): void {
        $updated = $this->db
            ->table('core_organization_memberships')
            ->where('id', $membershipId)
            ->update([
                'unit_id' => $defaultUnitId,
                'updated_at' => date('Y-m-d H:i:s'),
            ]);

        if ($updated === false) {
            throw new RuntimeException(
                'Gagal menyinkronkan unit_id legacy membership.'
            );
        }
    }

    private function getMembershipUnits(
        int $membershipId,
        int $organizationId
    ): array {
        return $this->db->table(
            'core_organization_membership_units mu'
        )
            ->select([
                'ou.id',
                'ou.code',
                'ou.name',
                'ou.unit_type',
                'mu.is_default',
            ])
            ->join(
                'core_organization_units ou',
                'ou.id = mu.organization_unit_id',
                'inner'
            )
            ->where('mu.membership_id', $membershipId)
            ->where('ou.organization_id', $organizationId)
            ->where('ou.status', 'active')
            ->where('ou.deleted_at', null)
            ->orderBy('mu.is_default', 'DESC')
            ->orderBy('ou.name', 'ASC')
            ->get()
            ->getResultArray();
    }

    private function findDefaultUnit(array $units): ?array
    {
        foreach ($units as $unit) {
            if ((int) $unit['is_default'] === 1) {
                return $unit;
            }
        }

        return null;
    }

    private function validateOrganization(int $organizationId): array
    {
        if ($organizationId <= 0) {
            throw new InvalidArgumentException(
                'Organization ID tidak valid.'
            );
        }

        $organization = $this->organizations->find($organizationId);

        if (
            !$organization
            || ($organization['status'] ?? null) !== 'active'
        ) {
            throw new RuntimeException(
                'Organisasi tidak ditemukan atau tidak aktif.'
            );
        }

        return $organization;
    }

    private function commitOrFail(string $message): void
    {
        if ($this->db->transStatus() === false) {
            throw new RuntimeException($message);
        }

        $this->db->transCommit();
    }

    private function getModelErrors(): string
    {
        $errors = $this->memberships->errors();

        return $errors !== []
            ? implode(' ', $errors)
            : 'Terjadi kesalahan saat menyimpan membership.';
    }
}
