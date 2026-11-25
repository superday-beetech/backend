<?php

declare(strict_types=1);

namespace Modules\Rbac\Models;

use Modules\Core\Models\OrganizationModel as BaseOrganizationModel;

/**
 * Compatibility adapter for legacy Rbac consumers.
 *
 * @deprecated Use Modules\Core\Models\OrganizationModel directly.
 * Core owns the organization domain and its persistence configuration.
 */
class CoreOrganizationModel extends BaseOrganizationModel
{
    /**
     * @deprecated Use the Core organization query/service APIs.
     */
    public function getActiveOrganizations(): array
    {
        return $this->where('status', 'active')
            ->where('deleted_at', null)
            ->orderBy('name', 'ASC')
            ->findAll();
    }
}
