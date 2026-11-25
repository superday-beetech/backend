<?php

declare(strict_types=1);

namespace Modules\Rbac\Models;

use Modules\Core\Models\OrganizationUnitModel as BaseOrganizationUnitModel;

/**
 * Compatibility adapter for legacy Rbac consumers.
 *
 * @deprecated Use Modules\Core\Models\OrganizationUnitModel directly.
 * Core owns the organization-unit domain and its persistence configuration.
 */
class CoreOrganizationUnitModel extends BaseOrganizationUnitModel
{
    /**
     * @deprecated Use the Core organization service APIs.
     */
    public function getActiveByOrganization(int $organizationId): array
    {
        return $this->where('organization_id', $organizationId)
            ->where('status', 'active')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
