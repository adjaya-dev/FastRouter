<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\Configurator;
use Adjaya\Router\RouteCollector;

class AddonConfigurator extends Configurator implements AddonConfiguratorInterface 
{
    protected $class = RouteCollector::class;
    
    protected $options = [
        'group' => GroupAddon::class,
        'route' => RouteAddon::class,
        'handlingRoute' => HandlingRouteAddon::class,
        'handlingGroup' => HandlingGroupAddon::class,
    ];
}
