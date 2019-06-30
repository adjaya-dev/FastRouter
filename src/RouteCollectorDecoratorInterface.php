<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface RouteCollectorDecoratorInterface extends RouteCollectorInterface
{
    public function groupAddons(
        callable $callback, CollectorInterface $collector
    ): HandlingGroupInterface;
}
