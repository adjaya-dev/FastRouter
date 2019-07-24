<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface HandlingGroupInterface
{
    public function path(string $path): self;

    public function name(string $name): self;
}
