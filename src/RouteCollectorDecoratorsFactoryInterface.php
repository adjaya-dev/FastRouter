<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface RouteCollectorDecoratorsFactoryInterface
{
    public function setDecoratorConfigurators(array $decorators): self;

    public function setDecoratorConfigurator(
        RouteCollectorDecoratorConfiguratorInterface $decorator
    ): self;

    /**
     * Return RouteCollectorDecoratorInterface.
     */
    public function decorate(RouteCollectorInterface $RouteCollector): RouteCollectorDecoratorInterface;
}
