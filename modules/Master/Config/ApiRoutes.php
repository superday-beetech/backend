<?php

declare(strict_types=1);

use CodeIgniter\Router\RouteCollection;

/** @var RouteCollection $routes */

$routes->get(
    'print-templates/(:num)/render.pdf',
    '\Modules\Master\Controllers\API\V1\PrintTemplatesController::renderPdf/$1'
);
$routes->get(
    'print-templates/(:num)/render',
    '\Modules\Master\Controllers\API\V1\PrintTemplatesController::render/$1'
);
$routes->group(
    '',
    ['namespace' => 'Modules\\Master\\Routers'],
    static function (RouteCollection $routes): void {
        $verbs = ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'];

        $routes->match(
            $verbs,
            '(:segment)/(:any)',
            'ApiRouter::route/$1/$2'
        );

        $routes->match(
            $verbs,
            '(:segment)',
            'ApiRouter::route/$1'
        );
    }
);
