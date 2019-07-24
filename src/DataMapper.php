<?php

declare(strict_types=1);

namespace Adjaya\Router;

class DataMapper
{
    protected $addons = [];

    public function setMap($adds, $value): void
    {
        $current = &$this->addons;

        foreach ((array) $adds as $add) {
            $current =  &$current[$add];
        }

        $current = $value;
    }

    public function setList($adds, $values): void
    {
        $current = &$this->addons;

        foreach ((array) $adds as $add) {
            $current =  &$current[(string) $add];
        }

        foreach ((array) $values as $value) {
            $current[] = $value;
        }
    }

    public function get($adds): array
    {
        $current = &$this->addons;

        foreach ((array) $adds as $add) {
            if (isset($current[$add])) {
                $current =  &$current[$add];
            } else {
                return [];
                break; 
            }
        }

        return $current;
    }

    public function getData(?array $parent_data = null): array
    {
        if ($parent_data) {
            return array_merge_recursive($parent_data, $this->addons);
        }
        
        return $this->addons;
    }
}
