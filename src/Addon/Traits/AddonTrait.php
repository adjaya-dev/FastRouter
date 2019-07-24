<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon\Traits;

trait AddonTrait
{
    protected $middlewares = [];

    protected $before = [];

    protected $after = [];

    public function setMiddlewares($middlewares)
    {
        foreach ((array) $middlewares as $middleware) {
            $this->middlewares[] = $middleware;
        }
    }

    public function getMiddlewares($parent = null)
    {
        if ($parent) {
            return array_merge_recursive($parent, $this->middlewares);
        }

        return $this->middlewares;
    }

    public function setBefore($beforeStack)
    {
        foreach ((array) $beforeStack as $middleware) {
            $this->before[] = $middleware;
        }
    }

    public function getBefore($parent = null) 
    {
        if ($parent) {
            return array_merge_recursive($parent, $this->before);
        }

        return $this->before;
    }

    public function setAfter($afterStack)
    {
        foreach ((array) $afterStack as $middleware) {
            $this->after[] = $middleware;
        }
    }

    public function getAfter($parent = null)
    {
        if ($parent) {
            return array_merge_recursive($parent, $this->after);
        }

        return $this->after;
    }

    public function getData(?array $parent = null): array
    {
        $data = null;

        if ($parent) { $data = $parent; }

        if ($middlewares = isset($parent['middlewares']) ? $this->getMiddlewares($parent['middlewares']) : $this->getMiddlewares()) {
            $data['middlewares'] = $middlewares;
        }

        if ($before = isset($parent['before']) ? $this->getBefore($parent['before']) : $this->getBefore()) {
            $data['before'] = $before;
        }

        if ($after = isset($parent['after']) ? $this->getAfter($parent['after']) : $this->getAfter()) {
            $data['after'] = $after;
        }

        return parent::getData($data);
    }
}
