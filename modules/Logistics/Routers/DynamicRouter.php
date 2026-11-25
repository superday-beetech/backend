<?php

declare(strict_types=1);

namespace Modules\Logistics\Routers;

use App\Libraries\DynamicRouterBase;

class DynamicRouter extends DynamicRouterBase
{
    protected string $moduleFolder = 'Logistics';

    protected string $moduleNamespace = 'Modules\\Logistics\\Controllers\\API\\V1';

    protected string $defaultController = 'DeliveryApiController';

    protected array $requiredRoles = [];

    protected array $requiredPermissions = [];
}
