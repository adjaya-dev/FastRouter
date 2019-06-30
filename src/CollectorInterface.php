<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface CollectorInterface
{
    public function __call(string $method, array $params): HandlingGroup;

    public function handle(): HandlingGroup;

    public function collection(callable $callback): HandlingGroup;

    public function group($prefix, callable $callback): HandlingGroup;

    public function route($httpMethods, $path, $handler): HandlingRoute;

    public function get($path, $handler): HandlingRoute;

    public function post($path, $handler): HandlingRoute;

    public function put($path, $handler): HandlingRoute;

    public function delete($path, $handler): HandlingRoute;

    public function patch($path, $handler): HandlingRoute;

    public function head($path, $handler): HandlingRoute;

    public function any($path, $handler): HandlingRoute;
}
