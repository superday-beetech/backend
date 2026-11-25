<?php

declare(strict_types=1);

namespace Modules\Rbac\Models;

use Modules\Core\Models\OrganizationModel as BaseOrganizationModel;

/**
 * Compatibility adapter for legacy callers.
 *
 * @deprecated Use Modules\Core\Models\OrganizationModel.
 */
class CoreOrganizationModel extends BaseOrganizationModel
{
}
