<?php

declare(strict_types=1);

namespace Adjaya\Router;

class RouteCollectorDecoratorBase implements RouteCollectorInterface, RouteCollectorDecoratorInterface
{
    /**
     * @var RouteCollector
     */
    protected $RouteCollector;

    protected $options;

    public function __construct(
        RouteCollectorInterface $RouteCollector, ?array $options = null
    ) {
        $this->RouteCollector = $RouteCollector;

        if (!empty($options)) {
            $this->setOptions($options);
        }
    }

    protected function setOptions(array $options): void
    {
        $this->options = $options;
    }

    public function getCurrentHandlingGroup(): HandlingGroupInterface
    {
        return $this->RouteCollector->getCurrentHandlingGroup();
    }

    public function getData(): array
    {
        return $this->RouteCollector->getData();
    }

    /**
     * Create an addons group.
     *
     * @param callable $callback
     */
    public function addCollection (
        callable $callback, CollectorInterface $collector
    ): HandlingGroupInterface {
        return $this->addGroup('', $callback, $collector);
    }



    public function addGroup(
        $prefix, callable $callback, CollectorInterface $collector
    ): HandlingGroupInterface 
    {
        return $this->RouteCollector->addGroup($prefix, $callback, $collector);
    }
    
    public function addRoute($httpMethods, $path, $handler): HandlingRouteInterface
    {
        return $this->RouteCollector->addRoute($httpMethods, $path, $handler);
    }
}
