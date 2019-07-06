<?php
// add function to class:
//https://gist.github.com/Mihailoff/3700483
// https://stackoverflow.com/questions/6384431/creating-anonymous-objects-in-php
// https://stackoverflow.com/questions/2938004/how-to-add-a-new-method-to-a-php-object-on-the-fly
// https://www.php.net/manual/fr/reserved.classes.php
declare(strict_types=1);

namespace Adjaya\Router;

use Adjaya\Router\DataGenerator\DataGeneratorInterface;
use Adjaya\Router\RouteParser\RouteParserInterface;
use Exception;

class RouteCollector implements RouteCollectorInterface
{
    protected $currentGroupId;
    protected $groupIdPrefix = 'group_';
    protected $groupIdCount = 0;
    protected $currentGroup;
    protected $currentHandlingGroup;

    /**
     * Routes Data.
     *
     * @var array
     */
    protected $routesData = [];

    protected $currentGroupPrefix = '';

    /**
     * @var string
     */
    protected $currentGroupName = '';

    //protected $currentGroupDataAddons = ['lists' => [], 'maps' => []];
    protected $currentGroupDataAddons = [];

    protected $Route = Route::class;
    protected $Group = Group::class;
    protected $HandlingGroup = HandlingGroup::class;
    protected $HandlingRoute = HandlingRoute::class;

    /**
     * Constructs a route collector.
     *
     * @param RouteParserInterface   $routeParser
     * @param DataGeneratorInterface $dataGenerator
     */
    public function __construct(RouteParserInterface $routeParser, DataGeneratorInterface $dataGenerator, ?array $options = [])
    {
        $this->routeParser = $routeParser;
        $this->dataGenerator = $dataGenerator;
        $this->currentGroup = new Group();
        $this->currentHandlingGroup = new HandlingGroup($this->currentGroup);

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

        //class_alias($routeHandling, __NAMESPACE__ . '\RouteHandling');
        //class_alias($groupHandling, __NAMESPACE__ . '\GroupHandling');
    }

    public function setRoute(string $routeClass)
    {
        if (is_a($routeClass, RouteInterface::class, true))
        {
            $this->Route = $routeClass;
        }
    }

    public function getRoute()
    {
        return $this->Route;
    }
 
    public function setGroup(string $groupClass)
    {
        if (is_a($groupClass, GroupInterface::class, true))
        {
            $this->Group = $groupClass;
        }
    }

    public function getGroup()
    {
        return $this->Group;
    }

    public function setHandlingRoute(string $handlingRouteClass)
    {
        if (is_a($handlingRouteClass, HandlingRouteInterface::class, true))
        {
            $this->HandlingRoute = $handlingRouteClass;
        }
    }

    public function getHandlingRoute()
    {
        return $this->Route;
    }

    public function setHandlingGroup(string $handlingGroupClass)
    {
        if (is_a($handlingGroupClass, HandlingGroupInterface::class, true))
        {
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
        $this->groupDataGenerator($this->currentGroup);

        $routes_data = $this->dataGenerator->getData();

        $routes_data['routes_data'] = $this->routesData;

        return $routes_data;
    }

    protected function groupDataGenerator($group)
    {
        $this->processCollection($group->getCollection());
    }

    protected function processCollection($collection) 
    {
        foreach ($collection as $obj) 
        {
            if ($obj instanceof RouteInterface) {

                $routeDataAddons = $obj->getData();

                $routeDataAddons = array_merge_recursive($this->currentGroupDataAddons, $routeDataAddons);

                $this->routesData['info'][$obj->getId()] = [
                    'handler' => $obj->getHandler(),
                    'add' => $routeDataAddons,
                ];

                $currentRoute = $this->currentGroupPrefix . $obj->getPath();
                $route_data = $this->routeParser->parse($currentRoute);

                $this->dataGenerator
                    ->addRoute($obj->getHttpMethods(), $route_data, $obj->getId(), $this->currentGroupId);

                if ($name = $obj->getName()) 
                {
                    $route_name = '';
        
                    if ($this->currentGroupName) {
                        $route_name = $this->currentGroupName;
                    }

                    $route_name .= $route_name ? '.' . $name : $name;
                    // Todo check if route_name is already set
                    $this->routesData['named'][$obj->getId()] = $route_name;

                    /* PARSE REVERSE */
                    if (method_exists($this->routeParser, 'parseReverse')) 
                    {
                        if (isset($this->routesData['reverse']) &&
                            array_key_exists($route_name, $this->routesData['reverse'])
                        ) {
                            throw new Exception(
                                "The route name '$route_name' is already used and must be unique!"
                            );
                        }
            
                        $this->routesData['reverse'][$route_name] = $this->routeParser->parseReverse($route_data);
                    }        
                }
            } elseif ($obj instanceof GroupInterface) {

                $previousGroupDataAddons = $this->currentGroupDataAddons;
                
                $groupDataAddons = $obj->getData();

                $this->currentGroupDataAddons = array_merge_recursive($previousGroupDataAddons, $groupDataAddons );

                $previousGroupName = $this->currentGroupName;
                if ($name = $obj->getName()) {
                    $this->currentGroupName = $previousGroupName ? $previousGroupName . '.' . $name : $name;
                }

                $previousGroupPrefix = $this->currentGroupPrefix;
                $this->currentGroupPrefix = $previousGroupPrefix . $obj->getPrefix();

                if ($prefix = $obj->getPrefix())
                {
                    $previousGroupId = $this->currentGroupId;
                    $this->currentGroupId = $this->groupIdPrefix . $this->groupIdCount++;

                    $group_data = $this->routeParser->parse($prefix);
                    $this->dataGenerator->addGroup($group_data, $this->currentGroupId, $previousGroupId);
                }

                $this->processCollection($obj->getCollection());

                if ($prefix) {
                    $this->currentGroupId = $previousGroupId;
                }

                $this->currentGroupPrefix = $previousGroupPrefix;
                $this->currentGroupName = $previousGroupName;

                $this->currentGroupDataAddons = $previousGroupDataAddons;

            } else {
                throw new Exception("Error Processing Request", 7);
            }
        }
    }

    public function addCollection(
        callable $callback, CollectorInterface $collector
    ): HandlingGroupInterface
    {
        return $this->addGroup('', $callback, $collector);
    }

    /**
     * {@inheritdoc}
     *
     * @param string|array $prefix
     */
    public function addGroup(
        $prefix, callable $callback, CollectorInterface $collector
    ): HandlingGroupInterface
    {
        $group_name = '';
        if (\is_array($prefix)) {
            $group_name = key($prefix);
            $prefix = $prefix[$group_name];
        }

        $previousGroup = $this->currentGroup;
        $group = $this->getGroup();
        $group = $this->currentGroup = new $group($prefix, $group_name);

        $previousHandlingGroup = $this->currentHandlingGroup;

        $groupHandling = $this->currentHandlingGroup = new $this->HandlingGroup($group);

        $previousGroup->addGroup($group);          

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
    public function addRoute($httpMethods, $path , $handler): HandlingRouteInterface
    {
        $name = '';
        if (\is_array($path)) {
            $name = key($path);
            $path = $path[key($path)];
        }
        
        $route = $this->getRoute();
        $route = new $route(
                    $httpMethods,
                    $path,
                    $handler,
                    $name
                );

        $this->currentGroup->addRoute($route);

        return new $this->HandlingRoute($route);
    }
}
