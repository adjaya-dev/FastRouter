<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\HandlingRoute;

class HandlingRouteAddon extends HandlingRoute
{
    use Traits\AddonTrait;

    public function param(string $name, $value): self 
    {
        $this->dataMapper->setParam($name, $value);
        /*
        $this->dataMapper->setMap(['params', $name], $value);
        var_dump($this->dataMapper->getMap(['params', $name]));
        */
        return $this;
    }
}
