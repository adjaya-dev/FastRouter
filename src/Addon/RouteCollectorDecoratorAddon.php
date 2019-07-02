<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\RouteCollectorDecoratorBase;
use Adjaya\Router\RouteCollectorInterface;

class RouteCollectorDecoratorAddon extends RouteCollectorDecoratorBase
{
    protected $RouteCollector;
    
    protected $options = [
        'route' => RouteAddon::class,
        'group' => GroupAddon::class,
        'handlingRoute' => HandlingRouteAddon::class,
        'handlingGroup' => HandlingGroupAddon::class,
    ];

    public function __construct(RouteCollectorInterface $RouteCollector, ?array $options = [])
    {
        $this->options = $options + $this->options;

        $this->RouteCollector = $RouteCollector;

        if (!empty($options)) {
            $this->setOptions($options);
        }

        $this->RouteCollector->setRoute($this->options['route']);
        $this->RouteCollector->setGroup($this->options['group']);
        $this->RouteCollector->setHandlingRoute($this->options['handlingRoute']);
        $this->RouteCollector->setHandlingGroup($this->options['handlingGroup']);
    }

    /**
     * Setting options.
     *
     * @param array $options
     */
    protected function setOptions(array $options): void
    {
        if (isset($options['route'])) {
            $this->options['route'] =  $options['route'];
        }

        if (isset($options['group'])) {
            $this->options['group'] = $options['group'];
        }

        if (isset($options['handlingRoute'])) {
            $this->options['handlingRoute'] =  $options['handlingRoute'];
        }

        if (isset($options['handlingGroup'])) {
            $this->options['handlingGroup'] = $options['handlingGroup'];
        }
    }
}
