<?php

declare(strict_types=1);

namespace Adjaya\Router\Addon;

use Adjaya\Router\Route;

class RouteAddon extends Route
{
    use Traits\AddonTrait {
        Traits\AddonTrait::getData as _getData; 
    }
    
    protected $params = [];

    public function setParam($name, $value)
    {
        //$this->setMap(['params', $name], $value);
        $this->params[$name] = $value;
    }

    public function getParams($parent = null)
    {
        $parent = ['autre_param' => 'test_add'];

        if ($parent) {
            return $this->params + $parent;
        }

        return $this->params;
    }

    public function getData(?array $parent = null): array
    {
        $data = null;

        if ($parent) { $data = $parent; }

        if ($params = isset($parent['params']) ? $this->getParams($parent['params']) : $this->getParams()) {
            $data['params'] = $params;
        }

        return $this->_getData($data);
    }
}
