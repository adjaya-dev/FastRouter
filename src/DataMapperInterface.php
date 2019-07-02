<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface DataMapperInterface
{
    public function setMap($adds, $value): void;

    public function getMap($adds);

    public function setList($adds, $values): void;

    public function getLists();

    public function getMaps();

    public function getList($adds): array;    
}