<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface GroupInterface
{
    public function getId(): string;

    public function addRoute(Route $route);

    public function addGroup(Group $group);

    public function setPrefix(string $prefix);

    public function getPrefix(): string;

    public function setName(string $name);

    public function getName(): string;

    public function getCollection(): array;

    public function getData(): array;
}
