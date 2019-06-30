<?php

declare(strict_types=1);

namespace Adjaya\Router;

class HandlingRoute implements HandlingRouteInterface
{
    protected $route;
    
    public function __construct(Route $route)
    {
        $this->route = $route;
    }

    public function name(string $name): HandlingRouteInterface
    {
        $this->route->setName($name);

        return $this;
    }

    public function path(string $path): HandlingRouteInterface
    {
        $this->route->setPath($path);

        return $this;
    }

    public function methods($httpMethods): HandlingRouteInterface
    {
        $this->route->setHttpMethods($httpMethods);

        return $this;
    }

    public function controller($controller): HandlingRouteInterface
    {
        $this->route->setHandler($controler);

        return $this;
    }
}
