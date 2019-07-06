<?php

declare(strict_types=1);

namespace Adjaya\Router;

class Configurator implements ConfiguratorInterface
{
    protected $options = [];

    protected $class;

    public function __construct(array $options)
    {
        $this->options = $options + $this->options;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function getClass(): string
    {
        return $this->class;
    }
}
