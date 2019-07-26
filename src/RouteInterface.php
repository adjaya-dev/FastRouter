<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface RouteInterface
{
    public function getId(): string;

    public function setName(string $name);

    public function getName(string $prefixName = ''): string;

    public function setPath(string $path);

    public function getPath($prefix = ''): string;

    public function setHttpMethods($httpMethods);

    public function getHttpMethods(): array;

    public function setHandler($handler);

    public function getHandler();

    public function getData(): array;
}
