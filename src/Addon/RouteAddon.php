<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\Route;

class RouteAddon extends Route
{
    public function setParam($name, $value)
    {
        $this->setMap(['params', $name], $value);
    }

    public function getParam($name)
    {
        return $this->getMap(['params', $name]);
    }

    public function getParams()
    {
        return $this->getMap('params');
    }
}