<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface ConfiguratorInterface
{
    public function getOptions(): array;

    public function getClass(): string;
}
