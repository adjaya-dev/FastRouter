<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\RouteCollectorDecoratorBase;
use Adjaya\Router\RouteCollectorInterface;

class RouteCollectorDecoratorAddon extends RouteCollectorDecoratorBase
{
    protected $RouteCollector;
    
    protected $options = [
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
        if (isset($options['handlingRoute'])) {
            $this->options['handlingRoute'] =  $options['handlingRoute'];
        }

        if (isset($options['handlingGroup'])) {
            $this->options['handlingGroup'] = $options['handlingGroup'];
        }
    }
}
