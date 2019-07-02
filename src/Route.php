<?php

declare(strict_types=1);

namespace Adjaya\Router;

class Route extends DataMapper implements RouteInterface
{
    protected static $idCount = 0;
    protected $id;
    protected $httpMethods;
    protected $path;
    protected $handler;
    protected $name;

    public function __construct($httpMethods = '*', $path = '/', $handler = null, $name = '')
    {
        var_dump('***** TODO function getData a utiser dans routeCollector::ProcessCollection ****************
        ');
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

    public function getName(): string
    {
        return $this->name;
    }

    public function setPath(string $path)
    {
        $this->path = $path;
    }

    public function getPath(): string
    {
        return $this->path;
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

    public function getData(): array
    {
        return [$this->getList(), $this->getMaps()];
    }
}
