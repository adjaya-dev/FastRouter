<?php

declare(strict_types=1);

namespace Adjaya\Router;

class HandlingRoute implements HandlingRouteInterface
{
    protected $dataMapper;

    public function __construct(RouteInterface $route)
    {
        $this->dataMapper = $route;
    }

    public function name(string $name): HandlingRouteInterface
    {
        $this->dataMapper->setName($name);

        return $this;
    }

    public function path(string $path): HandlingRouteInterface
    {
        $this->dataMapper->setPath($path);

        return $this;
    }

    public function methods($httpMethods): HandlingRouteInterface
    {
        $this->dataMapper->setHttpMethods($httpMethods);

        return $this;
    }

    public function controller($controller): HandlingRouteInterface
    {
        $this->dataMapper->setHandler($controller);

        return $this;
    }
}
