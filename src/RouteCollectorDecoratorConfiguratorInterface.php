<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface RouteCollectorDecoratorConfiguratorInterface extends ConfiguratorInterface
{
    public function decorate(object $routeCollector): RouteCollectorDecoratorInterface;
}
