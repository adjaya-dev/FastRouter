<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface GroupInterface
{
    public function getId(): string;

    public function addRoute(Route $route);

    public function addGroup(Group $group);

    public function setPath(string $path);

    public function getPath($prefix = ''): string;

    public function setName(string $name);

    public function getName(string $prefixName = ''): string;

    public function getCollection(): array;

    public function getData(): array;
}
