<?php

declare(strict_types=1);

namespace Modules\Logistics\Routers;

use App\Libraries\DynamicRouterBase;

class ApiRouter extends DynamicRouterBase
{
    protected string $moduleFolder = 'Logistics';

    protected string $moduleNamespace = 'Modules\\Logistic\\Controllers\\API\\V1';

    protected string $defaultController = 'DeliveryApiController';

}
