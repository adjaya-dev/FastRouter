<?php

declare(strict_types=1);

namespace Adjaya\Router;

class Group implements GroupInterface
{
    protected static $idCount = 0;
    protected $id;
    protected $path;
    protected $name;
    protected $collection = [];

    public function __construct(?string $path = '', ?string $name = '')
    {
        $this->id = 'group_' . self::$idCount++;
        $this->path = $path;
        $this->name = $name;
    }

    public function getId(): string
    {
        return $this->id;
    }

    public function addRoute(RouteInterface $route)
    {
        $this->collection[] = $route;
    }

    public function addGroup(GroupInterface $group)
    {
        $this->collection[] = $group;
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

    public function getCollection(): array
    {
        return $this->collection;
    }

    public function getData(?array $parent = null): array
    {
        $data = [];

        if ($parent) {
            $data = $parent;
        }

        if ($name = isset($parent['name']) ? $this->getName($parent['name']) : $this->getName()) {
            $data['name'] = $name;
        }

        if ($path = isset($parent['path']) ? $this->getPath($parent['path']) : $this->getPath()) {
            $data['path'] = $path;
        }

        return $data;
    }
}
