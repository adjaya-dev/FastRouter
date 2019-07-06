<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon\Traits;

trait AddonTrait
{
    public function middleWare(... $middleware)
    {
        $this->dataMapper->setList(['middlewares', 'basic'], $middleware);

        return $this;
    }

    public function before(... $before)
    {
        $this->dataMapper->setList(['middlewares', 'before'], $before);

        return $this;
    }

    public function after(... $after)
    {
        $this->dataMapper->setList(['middlewares', 'after'], $after);

        return $this;
    }
}