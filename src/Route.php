<?php

declare(strict_types=1);

namespace Adjaya\Router;

class Route implements RouteInterface
{
    protected static $idCount = 0;
    protected $id;
    protected $httpMethods;
    protected $path;
    protected $handler;
    protected $name;

    public function __construct($httpMethods = '*', $path = '/', $handler = null, $name = '')
    {
        $this->id = 'route_'. self::$idCount++;
        $this->httpMethods = (array) $httpMethods;
        $this->path = (string) $path;
        $this->handler = $handler;
        $this->name = (string) $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getName(string $prefixName = ''): string
    {
        if ($name = $this->name) {
            $name = $prefixName ? $prefixName . '.' . $name : $name;
        }
        
        return $name;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath($prefix = ''): string
    {
        if ($path = $this->path) {
            $path = $prefix ? $prefix . $path : $path;
        }
        
        return $path;
    }

    public function setHttpMethods($httpMethods)
    {
        $this->httpMethods = (array) $httpMethods;
    }

    public function getHttpMethods(): array
    {
        return $this->httpMethods;
    }

    public function setHandler($handler)
    {
        $this->handler = $handler;
    }

    public function getHandler()
    {
        return $this->handler;
    }

    public function getData(?array $parent = null): array
    {
        $data = [];

        if ($parent) { $data = $parent; }

        $data['id'] = $this->getId();

        if ($name = isset($parent['name']) ? $this->getName($parent['name']) : $this->getName()) {
            $data['name'] = $name;
        }

        if ($path = isset($parent['path']) ? $this->getPath($parent['path']) : $this->getPath()) {
            $data['path'] = $path;
        }

        if ($controller = $this->getHandler()) {
            $data['controller'] = $controller;
        }

        if ($methods = $this->getHttpMethods()) {
            $data['methods'] = $methods;
        }

        return $data;
    }
}
