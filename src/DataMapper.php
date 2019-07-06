<?php

declare(strict_types=1);

namespace Adjaya\Router;

class DataMapper
{
    protected $lists = [];
    
    protected $maps = [];

    public function setMap($adds, $value): void
    {
        $current = &$this->maps;

        foreach ((array) $adds as $add) {
            $current =  &$current[$add];
        }

        $current = $value;
    }

    public function getMap($adds): array
    {
        $current = &$this->maps;

        foreach ((array) $adds as $add) {
            if (isset($current[$add])) {
                $current =  &$current[$add];
            } else {
                return null;
                break; 
            }
        }

        return $current;
    }

    public function setList($adds, $values): void
    {
        $current = &$this->lists;

        foreach ((array) $adds as $add) {
            $current =  &$current[(string) $add];
        }

        foreach ((array) $values as $value) {
            $current[] = $value;
        }
    }

    public function getList($adds): array
    {
        $current = &$this->lists;
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

    public function getLists(): array
    {
        return $this->lists;
    }

    public function getMaps(): array
    {
        return $this->maps;
    }

    public function getData(): array
    {
        return array_merge_recursive($this->getLists(), $this->getMaps());
    }
}
