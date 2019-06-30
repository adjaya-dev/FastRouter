<?php

declare(strict_types=1);

namespace Adjaya\Router;

class HandlingGroup implements HandlingGroupInterface
{
    protected $group;

    public function __construct(Group $group)
    {
        $this->group = $group;
    }

    public function prefix(string $prefix): HandlingGroupInterface
    {
        $this->group->setPrefix($prefix);

        return $this;
    }

    public function name(string $name): HandlingGroupInterface
    {
        $this->group->setName($name);

        return $this;
    }
}