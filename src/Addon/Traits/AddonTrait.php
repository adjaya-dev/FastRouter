<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon\Traits;

trait AddonTrait
{
    protected $middlewares = [];

    protected $before = [];

    protected $after = [];

    protected $params = [];

    public function setMiddlewares($middlewares)
    {
        foreach ((array) $middlewares as $middleware) {
            $this->middlewares[] = $middleware;
        }
    }

    public function getMiddlewares($parent = null)
    {
        if ($parent) {
            foreach ($this->middlewares as $middleware) {
                $parent[] = $middleware;
            }

            return $parent;
        }

        return $this->middlewares;
    }

    public function setBefore($beforeStack)
    {
        foreach ((array) $beforeStack as $before) {
            $this->before[] = $before;
        }
    }

    public function getBefore($parent = null)
    {
        if ($parent) {
            foreach ($this->before as $before) {
                $parent[] = $before;
            }

            return $parent;
        }

        return $this->before;
    }

    public function setAfter($afterStack)
    {
        foreach ((array) $afterStack as $after) {
            $this->after[] = $after;
        }
    }

    public function getAfter($parent = null)
    {
        if ($parent) {
            foreach ($this->after as $after) {
                $parent[] = $after;
            }

            return $parent;
        }

        return $this->after;
    }

    public function setParam($name, $value)
    {
        $this->params[$name] = $value;
    }

    public function getParams($parent = null)
    {
        if ($parent) {
            return $this->params + $parent;
        }

        return $this->params;
    }

    public function getData(?array $parent = null): array
    {
        $data = null;

        if ($parent) {
            $data = $parent;
        }

        if ($middlewares = isset($parent['middlewares']) ? $this->getMiddlewares($parent['middlewares']) : $this->getMiddlewares()) {
            $data['middlewares'] = $middlewares;
        }

        if ($before = isset($parent['before']) ? $this->getBefore($parent['before']) : $this->getBefore()) {
            $data['before'] = $before;
        }

        if ($after = isset($parent['after']) ? $this->getAfter($parent['after']) : $this->getAfter()) {
            $data['after'] = $after;
        }

        if ($params = isset($parent['params']) ? $this->getParams($parent['params']) : $this->getParams()) {
            $data['params'] = $params;
        }

        return parent::getData($data);
    }
}
