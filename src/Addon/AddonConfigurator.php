<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;
use Adjaya\Router\Configurator;

use Adjaya\Router\RouteCollectorDecoratorInterface;

class AddonConfigurator implements AddonConfiguratorInterface
{
    protected $addonClass = RouteCollectorDecoratorAddon::class;

    protected $options;

    public function __construct(array $options)
    {
        $this->options = $options;
    }

    public function decorate(object $routeCollector): RouteCollectorDecoratorInterface
    {
        $addonClass = $this->getClass();

        return new $addonClass($routeCollector, $this->getOptions());
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getClass(): string
    {
        return $this->addonClass;
    }
}
