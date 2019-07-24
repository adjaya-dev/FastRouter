<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon\Traits;

trait HandlingAddonTrait
{
    public function middleWare(... $middlewares)
    {
        //$this->dataMapper->setList(['middlewares'], $middlewares);
        $this->dataMapper->setMiddlewares($middlewares);


        return $this;
    }

    public function before(... $before)
    {
        //$this->dataMapper->setList(['filters', 'before'], $before);
        $this->dataMapper->setBefore($before);

        return $this;
    }

    public function after(... $after)
    {
        //$this->dataMapper->setList(['filters', 'after'], $after);
        $this->dataMapper->setAfter($after);

        return $this;
    }
}
