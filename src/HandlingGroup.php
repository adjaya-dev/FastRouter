<?php

declare(strict_types=1);

namespace Adjaya\Router;

class HandlingGroup implements HandlingGroupInterface
{
    protected $dataMapper;

    public function __construct(Group $group)
    {
        $this->dataMapper = $group;
    }

    public function path(string $path): HandlingGroupInterface
    {
        $this->dataMapper->setPath($path);

        return $this;
    }

    public function name(string $name): HandlingGroupInterface
    {
        $this->dataMapper->setName($name);

        return $this;
    }
}
