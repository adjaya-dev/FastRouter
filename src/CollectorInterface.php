<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface CollectorInterface
{
    public function __call(string $method, array $params): HandlingGroupInterface;

    public function handle(): HandlingGroupInterface;

    public function collection(callable $callback): HandlingGroupInterface;

    public function group($prefix, callable $callback): HandlingGroupInterface;

    public function route($path, $handler, $httpMethods): HandlingRouteInterface;

    public function get($path, $handler): HandlingRouteInterface;

    public function post($path, $handler): HandlingRouteInterface;

    public function put($path, $handler): HandlingRouteInterface;

    public function delete($path, $handler): HandlingRouteInterface;

    public function patch($path, $handler): HandlingRouteInterface;

    public function head($path, $handler): HandlingRouteInterface;

    public function any($path, $handler): HandlingRouteInterface;
}
