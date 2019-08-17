<?php

declare(strict_types=1);

namespace Adjaya\Router;

use Adjaya\Router\DataGenerator\DataGeneratorInterface;
use Adjaya\Router\RouteParser\RouteParserInterface;
use Exception;

class RouteCollector implements RouteCollectorInterface
{
    protected $Route = Route::class;
    protected $Group = Group::class;
    protected $HandlingGroup = HandlingGroup::class;
    protected $HandlingRoute = HandlingRoute::class;

    protected $currentGroup;
    protected $currentHandlingGroup;

    protected $groupIdPrefix = 'group_';
    protected $groupIdCount = 0;
    protected $currentGroupId;
    protected $currentGroupData = [];

    /**
     * Routes Data.
     *
     * @var array
     */
    protected $routesData = ['named' =>[]];

    /**
     * Constructs a route collector.
     *
     * @param RouteParserInterface   $routeParser
     * @param DataGeneratorInterface $dataGenerator
     * @param   array|null           $options
     */
    public function __construct(RouteParserInterface $routeParser, DataGeneratorInterface $dataGenerator, ?array $options = [])
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;

        if (isset($options['route'])) {
            $this->setRoute($options['route']);
        }

        if (isset($options['group'])) {
            $this->setGroup($options['group']);
        }

        if (isset($options['handlingRoute'])) {
            $this->setHandlingRoute($options['handlingRoute']);
        }

        if (isset($options['handlingGroup'])) {
            $this->setHandlingGroup($options['handlingGroup']);
        }

        $this->currentGroup = new $this->Group();
        $this->currentHandlingGroup = new $this->HandlingGroup($this->currentGroup);
    }

    public function setRoute(string $routeClass)
    {
        if (is_a($routeClass, RouteInterface::class, true)) {
            $this->Route = $routeClass;
        }
    }

    public function setGroup(string $groupClass)
    {
        if (is_a($groupClass, GroupInterface::class, true)) {
            $this->Group = $groupClass;
        }
    }

    public function setHandlingRoute(string $handlingRouteClass)
    {
        if (is_a($handlingRouteClass, HandlingRouteInterface::class, true)) {
            $this->HandlingRoute = $handlingRouteClass;
        }
    }

    public function setHandlingGroup(string $handlingGroupClass)
    {
        if (is_a($handlingGroupClass, HandlingGroupInterface::class, true)) {
            $this->HandlingGroup = $handlingGroupClass;
        }
    }

    public function getCurrentHandlingGroup(): HandlingGroupInterface
    {
        return $this->currentHandlingGroup;
    }

    /**
     * {@inheritdoc}
     *
     * Generate Main Group data
     */
    public function getData(): array
    {
        $this->processGroup($this->currentGroup);

        $routes_data = $this->dataGenerator->getData();

        $routes_data['routes_data'] = $this->routesData;

        return $routes_data;
    }

    protected function processGroup(GroupInterface $group)
    {
        $collection = $group->getCollection();

        foreach ($collection as $obj) {
            if ($obj instanceof RouteInterface) {
                $data = $obj->getData($this->currentGroupData);
                $this->routesData['info'][$data['id']] = $data;

                $route_data = $this->routeParser->parse($data['path']);

                $this->dataGenerator
                    ->addRoute(
                        $data['methods'],
                        $route_data,
                        $data['id'],
                        $this->currentGroupId
                    );
                if (isset($data['name'])) {

                    if (in_array($data['name'], $this->routesData['named'])) {
                    //if (isset($this->routesData['named'][$data['id']])) {
                        throw new Exception(
                            "The route name {$data['name']} is already used and must be unique!"
                        );
                    }
    
                    $this->routesData['named'][$data['id']] = $data['name'];
    
                    /* PARSE REVERSE */
                    if (method_exists($this->routeParser, 'parseReverse')) {
                        $this->routesData['reverse'][$data['name']] = $this->routeParser->parseReverse($route_data);
                    }
                }
            } elseif ($obj instanceof GroupInterface) {
                $previousGroupData = $this->currentGroupData;
                $this->currentGroupData = $obj->getData($previousGroupData);

                if ($prefix = $obj->getPath()) {
                    $previousGroupId = $this->currentGroupId;
                    $this->currentGroupId = $this->groupIdPrefix . $this->groupIdCount++;

                    $group_data = $this->routeParser->parse($prefix);
                    $this->dataGenerator->addGroup($group_data, $this->currentGroupId, $previousGroupId);
                }

                $this->processGroup($obj);

                if ($prefix) {
                    $this->currentGroupId = $previousGroupId;
                }

                $this->currentGroupData = $previousGroupData;
            } else {
                throw new Exception('Error Processing Request', 7);
            }
        }
    }

    public function addCollection(
        callable $callback,
        CollectorInterface $collector
    ): HandlingGroupInterface {
        return $this->addGroup('', $callback, $collector);
    }

    /**
     * {@inheritdoc}
     *
     * @param string|array $prefix
     */
    public function addGroup(
        $path, callable $callback, CollectorInterface $collector): HandlingGroupInterface {
        $group_name = '';
        if (\is_array($path)) {
            $group_name = key($path);
            $path = $path[$group_name];
        }

        $previousGroup = $this->currentGroup;

        $this->currentGroup = new $this->Group($path, $group_name);

        $previousHandlingGroup = $this->currentHandlingGroup;

        $groupHandling = $this->currentHandlingGroup = new $this->HandlingGroup($this->currentGroup);

        $previousGroup->addGroup($this->currentGroup);

        $callback($collector);

        $this->currentGroup = $previousGroup;
        $this->currentHandlingGroup = $previousHandlingGroup;

        return $groupHandling;
    }

    /**
     * {@inheritdoc}
     *
     * @param string|array $route
     *
     * @return string $route_id
     */
    public function addRoute($httpMethods, $path, $handler): HandlingRouteInterface
    {
        $name = '';
        if (\is_array($path)) {
            $name = key($path);
            $path = $path[key($path)];
        }

        $route = new $this->Route(
            $httpMethods,
            $path,
            $handler,
            $name
        );

        $this->currentGroup->addRoute($route);

        return new $this->HandlingRoute($route);
    }
}
