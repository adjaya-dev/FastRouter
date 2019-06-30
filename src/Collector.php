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

    public function __call(string $method, array $params): HandlingGroup
    {
        try {
            return call_user_func_array(
                [$this->handle(), $method], $params
            );
        } catch (Throwable $e) {
            throw new Exception ($e->getMessage());
        }
    }

    public function handle(): HandlingGroup
    {
        return $this->routeCollector->getCurrentHandlingGroup();
    }

    public function group($prefix, callable $callback): HandlingGroup
    {
        return $this->routeCollector->addGroup($prefix, $callback, $this);
    }

    public function route($httpMethods, $path, $handler): HandlingRoute
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
    public function get($path, $handler): HandlingRoute
    {
        return $this->route('GET', $path, $handler);
    }

    /**
     * Adds a POST route to the collection.
     *
     * This is simply an alias of $this->route('POST', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function post($path, $handler): HandlingRoute
    {
        return $this->route('POST', $path, $handler);
    }

    /**
     * Adds a PUT route to the collection.
     *
     * This is simply an alias of $this->route('PUT', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function put($path, $handler): HandlingRoute
    {
        return $this->route('PUT', $path, $handler);
    }

    /**
     * Adds a DELETE route to the collection.
     *
     * This is simply an alias of $this->route('DELETE', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function delete($path, $handler): HandlingRoute
    {
        return $this->route('DELETE', $path, $handler);
    }

    /**
     * Adds a PATCH route to the collection.
     *
     * This is simply an alias of $this->route('PATCH', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function patch($path, $handler): HandlingRoute
    {
        return $this->route('PATCH', $path, $handler);
    }

    /**
     * Adds a HEAD route to the collection.
     *
     * This is simply an alias of $this->route('HEAD', $path, $handler)
     *
     * @param string $path
     * @param mixed  $handler
     */
    public function head($path, $handler): HandlingRoute
    {
        return $this->route('HEAD', $path, $handler);
    }

    public function any($path, $handler): HandlingRoute
    {
        return $this->route('*', $path, $handler);
    }
}