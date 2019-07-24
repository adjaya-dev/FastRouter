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
 
    public function setGroup(string $groupClass)
    {
        if (is_a($groupClass, GroupInterface::class, true))
        {
            $this->Group = $groupClass;
        }
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
        $this->processGroup($group);
    }

    protected function processGroup($group) 
    {
        $collection = $group->getCollection();

        foreach ($collection as $obj) 
        {
            if ($obj instanceof RouteInterface) 
            {
                $routeInfo = [];

                $currentRoute = $obj->getPath($this->currentGroupPrefix);

                $route_data = $this->routeParser->parse($currentRoute);

                $this->dataGenerator
                    ->addRoute($obj->getHttpMethods(), $route_data, $obj->getId(), $this->currentGroupId);

                if ($route_name = $obj->getName($this->currentGroupName)) 
                {
                    $routeInfo += ['name' => $route_name];

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

                $routeInfo['handler'] = $obj->getHandler();
                
                if ($add = $obj->getData($this->currentGroupDataAddons))
                {
                    $routeInfo['add'] = $add;
                }

                $this->routesData['info'][$obj->getId()] = $routeInfo;

            } 
            elseif ($obj instanceof GroupInterface) 
            {
                if ($prefix = $obj->getPath())
                {
                    $previousGroupId = $this->currentGroupId;
                    $this->currentGroupId = $this->groupIdPrefix . $this->groupIdCount++;

                    $group_data = $this->routeParser->parse($prefix);
                    $this->dataGenerator->addGroup($group_data, $this->currentGroupId, $previousGroupId);
                }

                $previousGroupDataAddons = $this->currentGroupDataAddons;

                $this->currentGroupDataAddons = $obj->getData($previousGroupDataAddons);

                $previousGroupName = $this->currentGroupName;
                $this->currentGroupName = $obj->getName($previousGroupName);

                $previousGroupPrefix = $this->currentGroupPrefix;
                $this->currentGroupPrefix = $obj->getPath($previousGroupPrefix);

                $this->processGroup($obj);

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
        $path, callable $callback, CollectorInterface $collector
    ): HandlingGroupInterface
    {
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
    public function addRoute($httpMethods, $path , $handler): HandlingRouteInterface
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
