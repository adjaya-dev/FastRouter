<?php

declare(strict_types=1);

namespace Adjaya\Router;

class RouteCollectorDecoratorConfigurator extends Configurator implements RouteCollectorDecoratorConfiguratorInterface
{
    public function decorate(object $routeCollector): RouteCollectorDecoratorInterface
    {
        $decoratorClass = $this->getClass();

        return new $decoratorClass($routeCollector, $this->getOptions());
    }
}
