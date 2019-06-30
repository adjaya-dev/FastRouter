<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface HandlingRouteInterface
{
    public function name(string $name): self;

    public function path(string $path): self;

    public function methods($httpMethods): self;

    public function controller($controller): self;
}
