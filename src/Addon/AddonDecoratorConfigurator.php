<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\RouteCollectorDecoratorConfigurator;

class AddonDecoratorConfigurator extends RouteCollectorDecoratorConfigurator implements AddonDecoratorConfiguratorInterface
{
    protected $class = RouteCollectorDecoratorAddon::class;
}
