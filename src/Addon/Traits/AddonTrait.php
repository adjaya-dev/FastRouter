<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon\Traits;

trait AddonTrait
{
    protected $middleWares = [];

    public function middleWare(... $middleware)
    {
        echo 'midddddleware';
        $this->route->setList(['middlewares', 'basic'], $middleware);
        var_dump($this->route->getList('middlewares'));

        return $this;
    }

    public function before(... $before)
    {
        $this->route->setList(['middlewares', 'before'], $before);

        return $this;
    }

    public function after(... $after)
    {
        $this->route->setList(['middlewares', 'after'], $after);

        return $this;
    }
}