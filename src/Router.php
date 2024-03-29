<?php

declare(strict_types=1);

namespace Adjaya\Router;

use Exception;
use LogicException;
use RuntimeException;

class Router
{
    /**
     * @var array|null
     */
    protected $routesData;

    /**
     * @var DispatcherInterface
     */
    protected $dispatcher;

    /**
     * @var callable
     */
    protected $routeDefinitionCallback;

    /**
     * Router options.
     *
     * @var array
     */
    protected $options = [
        'collector'                       => Collector::class,
        'routeCollector'                  => RouteCollector::class,
        'routeParser'                     => RouteParser\Std::class,
        'dataGenerator'                   => DataGenerator\MarkBased::class,
        'dispatcher'                      => Dispatcher\MarkBased::class,
        'cacheDisabled'                   => false,
    ];

    /**
     * @param callable   $routeDefinitionCallback
     * @param array|null $options
     */
    public function __construct(callable $routeDefinitionCallback, ?array $options = [])
    {
        $this->routeDefinitionCallback = $routeDefinitionCallback;

        $this->options = $options + $this->options;
        /*/
        var_dump('************Router Options');
        echo '<pre>';
        print_r($this->options);
        echo '</pre>';
        //*/
    }

    /**
     * Set simple Dispatcher.
     */
    public function setSimpleDispatcher(): void
    {
        $dispatchData = $this->simpleRoutes();

        $this->dispatcher = $this->getDispatcher($dispatchData);
    }

    /**
     * Set cached Dispatcher.
     */
    public function setCachedDispatcher(): void
    {
        $dispatchData = $this->cachedRoutes();

        $this->dispatcher = $this->getDispatcher($dispatchData);
    }

    public function simpleRoutes(): array
    {
        $routeCollector = $this->getRouteCollector();
        $collector = $this->getCollector($routeCollector);

        $routeDefinitionCallback = $this->routeDefinitionCallback;
        $routeDefinitionCallback($collector);

        return $routeCollector->getData();
    }

    public function cachedRoutes(): array
    {
        $options = $this->options;

        if (!isset($options['cacheFile'])) {
            throw new LogicException('Must specify "cacheFile" option');
        }

        if (!$options['cacheDisabled'] && file_exists($options['cacheFile'])) {
            $dispatchData = require $options['cacheFile'];
            if (!\is_array($dispatchData)) {
                throw new RuntimeException('Invalid cache file "' . $options['cacheFile'] . '"');
            }

            return $dispatchData;
        }

        $dispatchData = $this->simpleRoutes();

        if (!is_dir(dirname($options['cacheFile'])))
        {
            mkdir(dirname($options['cacheFile']), 0755, true);
        }

        file_put_contents(
            $options['cacheFile'],
            '<?php return ' . var_export($dispatchData, true) . ';'
        );

        return $dispatchData;
    }

    /**
     * @param array &$dispatchData
     */
    protected function setRoutesData(array &$dispatchData): void
    {
        if (isset($dispatchData['routes_data'])) {
            $this->routesData = $dispatchData['routes_data'];
            unset($dispatchData['routes_data']);
        }
    }

    /**
     * @param string $method
     * @param string $path
     *
     * @return array $routeInfo
     */
    public function dispatch(string $method, string $path): array
    {
        if ($this->dispatcher) {
            $routeInfo = $this->dispatcher->dispatch($method, $path);

            return $routeInfo;
        }
        throw new LogicException('Dispatcher must be set first');
    }

    protected function getDispatcher(array $dispatchData): Dispatcher\DispatcherInterface
    {
        $this->setRoutesData($dispatchData);

        return new $this->options['dispatcher']($dispatchData, $this->routesData);
    }

    protected function getRouteCollector(): RouteCollectorInterface
    {
        if (\is_array($this->options['routeCollector'])) {
            list($routeCollector, $options) = $this->options['routeCollector'];
        } else {
            $routeCollector = $this->options['routeCollector'];
            $options = [];
        }

        if (is_a($routeCollector, ConfiguratorInterface::class, true)) {
            $routeCollector = new $routeCollector($options);
            $options = $routeCollector->getOptions();
            $routeCollector = $routeCollector->getClass();
        }

        if (is_a($routeCollector, RouteCollectorInterface::class, true)) {
            if (isset($options['routeParser'])) {
                $this->options['routeParser'] = $options['routeParser'];
                unset($options['routeParser']);
            }
            if (isset($options['dataGenerator'])) {
                $this->options['dataGenerator'] = $options['dataGenerator'];
                unset($options['dataGenerator']);
            }
            if (isset($options['allowIdenticalRegexRoutes'])) {
                $this->options['allowIdenticalRegexRoutes'] = $options['allowIdenticalRegexRoutes'];
                unset($options['allowIdenticalRegexRoutes']);
            }

            return $this->routeCollectorFactory($routeCollector, $options);
        }

        throw new Exception('Error Processing Request', 1);
    }

    protected function routeCollectorFactory($routeCollector, $options): RouteCollectorInterface
    {
        return new $routeCollector(
            $this->getRouteParser(),
            $this->getDataGenerator(),
            $options
        );
    }

    protected function getCollector(RouteCollectorInterface $routeCollector): CollectorInterface
    {
        return new $this->options['collector']($routeCollector);
    }

    protected function getRouteParser(): RouteParser\RouteParserInterface
    {
        return new $this->options['routeParser']();
    }

    protected function getDataGenerator(): DataGenerator\DataGeneratorInterface
    {
        $DataGenerator = new $this->options['dataGenerator']();

        if (isset($options['allowIdenticalRegexRoutes']) && !$options['allowIdenticalRegexRoutes']) { // Default true
            $DataGenerator->allowIdenticalsRegexRoutes(false);
        }

        return $DataGenerator;
    }

    protected function getConfigurator(string $configuratorClass, array $options): ConfiguratorInterface
    {
        return new $configuratorClass($options);
    }

    /**
     * Get routes Data.
     *
     * @return array
     */
    public function getRoutesData(): array
    {
        return $this->routesData;
    }

    public function getRoutesInfo(): array
    {
        return $this->routesData['info'];
    }

    /**
     * @param string $id Route id
     *
     * @return array|null
     */
    public function getRouteInfo(string $id): ?array
    {
        return !isset($this->routesData['info'][$id]) ? null : $this->routesData['info'][$id];
    }

    /**
     * @return array
     */
    public function getReverseRoutesData(): array
    {
        if (isset($this->routesData['reverse'])) {
            return $this->routesData['reverse'];
        }

        throw new Exception('Not reverse data found!');
    }

    /**
     * @return ReverseRouter
     */
    public function getReverseRouter(): ReverseRouter
    {
        if (method_exists($this->options['routeParser'], 'getReverseRouter')) {
            return
            ($this->options['routeParser'])::getReverseRouter($this->getReverseRoutesData());
        }

        throw new RuntimeException($this->options['routeParser'] . '::getReverseRouter does not exist');
    }
}
