<?php

declare(strict_types=1);

namespace Modules\Rbac\Models;

use Modules\Core\Models\OrganizationUnitModel as BaseOrganizationUnitModel;

/**
 * Compatibility adapter for legacy callers.
 *
 * @deprecated Use Modules\Core\Models\OrganizationUnitModel.
 */
class CoreOrganizationUnitModel extends BaseOrganizationUnitModel
{
}
