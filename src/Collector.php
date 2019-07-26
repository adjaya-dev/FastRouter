<?php

declare(strict_types=1);

namespace Adjaya\Router;

use Exception;
use Throwable;

class Collector implements CollectorInterface
{
    protected $routeCollector;

    public function __construct(RouteCollectorInterface $routeCollector)
    {
        $this->routeCollector = $routeCollector;
    }

    public function __call(string $method, array $params): HandlingGroupInterface
    {
        try {
            return \call_user_func_array(
                [$this->handle(), $method],
                $params
            );
        } catch (Throwable $e) {
            throw new Exception($e->getMessage());
        }
    }

    public function handle(): HandlingGroupInterface
    {
        return $this->routeCollector->getCurrentHandlingGroup();
    }

    public function collection(callable $callback): HandlingGroupInterface
    {
        return $this->routeCollector->addCollection($callback, $this);
    }

    public function group($prefix, callable $callback): HandlingGroupInterface
    {
        return $this->routeCollector->addGroup($prefix, $callback, $this);
    }

    public function route($path = '/', $handler = null, $httpMethods = '*'): HandlingRouteInterface
    {
        return $this->routeCollector->addRoute($httpMethods, $path, $handler);
    }

    /**
     * Adds a GET route to the collection.
     *
     * This is simply an alias of $this->route('GET', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function get($path = '/', $handler = null): HandlingRouteInterface
    {
        return $this->route($path, $handler, 'GET');
    }

    /**
     * Adds a POST route to the collection.
     *
     * This is simply an alias of $this->route('POST', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function post($path = '/', $handler = null): HandlingRouteInterface
    {
        return $this->route($path, $handler, 'POST');
    }

    /**
     * Adds a PUT route to the collection.
     *
     * This is simply an alias of $this->route('PUT', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function put($path = '/', $handler = null): HandlingRouteInterface
    {
        return $this->route($path, $handler, 'PUT');
    }

    /**
     * Adds a DELETE route to the collection.
     *
     * This is simply an alias of $this->route('DELETE', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function delete($path = '/', $handler = null): HandlingRouteInterface
    {
        return $this->route($path, $handler, 'DELETE');
    }

    /**
     * Adds a PATCH route to the collection.
     *
     * This is simply an alias of $this->route('PATCH', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function patch($path = '/', $handler = null): HandlingRouteInterface
    {
        return $this->route($path, $handler, 'PATCH');
    }

    /**
     * Adds a HEAD route to the collection.
     *
     * This is simply an alias of $this->route('HEAD', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function head($path = '/', $handler = null): HandlingRouteInterface
    {
        return $this->route($path, $handler, 'HEAD');
    }

    public function any($path = '/', $handler = null): HandlingRouteInterface
    {
        return $this->route($path, $handler, '*');
    }
}
