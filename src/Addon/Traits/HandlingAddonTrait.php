<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon\Traits;

trait HandlingAddonTrait
{
    public function middleWare(...$middlewares): self
    {
        $this->dataMapper->setMiddlewares($middlewares);

        return $this;
    }

    public function before(...$before): self
    {
        $this->dataMapper->setBefore($before);

        return $this;
    }

    public function after(...$after): self
    {
        $this->dataMapper->setAfter($after);

        return $this;
    }

    public function param(string $name, $value): self
    {
        $this->dataMapper->setParam($name, $value);

        return $this;
    }
}
