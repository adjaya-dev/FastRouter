<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\HandlingRoute;

class HandlingRouteAddon extends HandlingRoute
{
    use Traits\HandlingAddonTrait;

    public function param(string $name, $value): self 
    {
        $this->dataMapper->setParam($name, $value);

        return $this;
    }
}
