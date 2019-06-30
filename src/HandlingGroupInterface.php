<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface HandlingGroupInterface
{
    public function prefix(string $prefix): self;

    public function name(string $name): self;
}
