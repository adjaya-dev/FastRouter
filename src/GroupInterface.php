<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface GroupInterface
{
    public function getId(): string;

    public function addRoute(RouteInterface $route);

    public function addGroup(self $group);

    public function setPath(string $path);

    public function getPath($prefix = ''): string;

    public function setName(string $name);

    public function getName(string $prefixName = ''): string;

    public function getCollection(): array;

    public function getData(?array $parent = null): array;
}
