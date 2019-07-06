<?php

declare(strict_types=1);

namespace Adjaya\Router;

interface DataMapperInterface
{
    public function setMap($adds, $value): void;

    public function getMap($adds): array;

    public function setList($adds, $values): void;

    public function getLists(): array;

    public function getMaps(): array;

    public function getList($adds): array;  

    public function getData(): array;
}