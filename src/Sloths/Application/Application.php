<?php

namespace Sloths\Application;

use Sloths\Application\Exception\Pass;
use Sloths\Application\Exception\NotFound;
use Sloths\Application\Service\ServiceInterface;
use Sloths\Misc\ConfigurableTrait;
use Sloths\Observer\ObserverTrait;
use \Closure;

/**
 * Class Application
 * @package Sloths\Application
 *
 * @property Service\Request $request
 * @property Service\Response $response
 * @property Service\View $view
 * @property Service\Router $router
 * @property Service\Config $config
 * @property Service\Session $session
 * @property Service\Messages $messages
 */
class Application
{
    use ObserverTrait;

    /**
     * @var string
     */
    protected $applicationDirectory;

    /**
     * @var string
     */
    protected $requestBasePath;

    /**
     * @var string
     */
    protected $env = 'production';

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var array
     */
    protected $configDirectories = [];

    /**
     * @var bool
     */
    protected $stopped = false;

    /**
     * @var array
     */
    protected $services = [
        'exceptionHandler'  => 'Sloths\Exception\Handler',
        'config'            => 'Sloths\Misc\Config',
        'request'           => 'Sloths\Http\Request',
        'response'          => 'Sloths\Http\Response',
        'view'              => 'Sloths\Application\Service\View',
        'router'            => 'Sloths\Application\Service\Router',
        'session'           => 'Sloths\Session\Session',
        'flash'             => 'Sloths\Session\Flash',
        'messages'          => 'Sloths\Session\Messages'
    ];

    /**
     * @var array
     */
    protected $shortcutMethods = [
        'map'       => ['router', 'map'],
        'head'      => ['router', 'head'],
        'get'       => ['router', 'get'],
        'post'      => ['router', 'post'],
        'put'       => ['router', 'put'],
        'patch'     => ['router', 'patch'],
        'delete'    => ['router', 'delete'],
        'options'   => ['router', 'options'],
        'trace'     => ['router', 'trace'],
        'isXhr'     => ['request', 'isXhr'],
        'render'    => ['view', 'render'],
        'setLayout' => ['view', 'setLayout'],
    ];

    /**
     * @var array
     */
    protected $shortcutProperties = [
        'params'    => ['request', 'params'],
        'get'       => ['request', 'get'],
        'post'      => ['request', 'post'],
        'cookie'    => ['request', 'cookie'],
        'headers'   => ['request', 'headers'],
    ];

    /**
     * @param string $requestBasePath
     */
    final public function __construct($requestBasePath = '')
    {
        $this->requestBasePath = $requestBasePath;
        $this->initialize();
    }

    protected function initialize() {}

    public function __get($name)
    {
        if ($service = $this->getService($name)) {
            return $service;
        }

        if (isset($this->shortcutProperties[$name])) {
            $meta = $this->shortcutProperties[$name];

            return $this->{$meta[0]}->{$meta[1]};
        }

        throw new \InvalidArgumentException(sprintf('Call to undefined property %s', $name));
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if ($this->hasShortcutMethod($method)) {
            $alias = $this->shortcutMethods[$method];
            $callback = [$this->{$alias[0]}, $alias[1]];
            return call_user_func_array($callback, $args);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s', $method));
    }

    /**
     * @param string $env
     * @return $this
     */
    public function setEnv($env)
    {
        $this->env = $env;
        return $this;
    }

    /**
     * @return string
     */
    public function getEnv()
    {
        return $this->env;
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function setDebug($state)
    {
        $this->debug = $state;
        return $this;
    }

    /**
     * @return bool
     */
    public function getDebug()
    {
        return $this->debug;
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->applicationDirectory = $directory;

        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        if (!$this->applicationDirectory) {
            $reflection = new \ReflectionClass(get_called_class());
            $classFile = $reflection->getFileName();
            $this->applicationDirectory = pathinfo($classFile, PATHINFO_DIRNAME);
        }

        return $this->applicationDirectory;
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function addConfigDirectory($directory)
    {
        $this->configDirectories[$directory] = $directory;
        return $this;
    }

    /**
     * @param array $directories
     * @return $this
     */
    public function addConfigDirectories(array $directories)
    {
        foreach ($directories as $directory) {
            $this->addConfigDirectory($directory);
        }

        return $this;
    }

    /**
     * @return array
     */
    public function getConfigDirectories()
    {
        if (!$this->configDirectories) {
            $defaultDirectory = $this->getDirectory() . '/config';
            $this->configDirectories[$defaultDirectory] = $defaultDirectory;
        }

        return $this->configDirectories;
    }

    /**
     * @param string $name
     * @param mixed $service
     * @return $this
     * @throws \RuntimeException
     */
    public function addService($name, $service)
    {
        if ($this->hasService($name)) {
            throw new \RuntimeException(sprintf('Service %s already exists', $name));
        }

        return $this->setService($name, $service);

    }

    /**
     * @param array $services
     * @return $this
     */
    public function addServices(array $services)
    {
        foreach ($services as $name => $service) {
            $this->addService($name, $service);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $service
     * @return $this
     */
    public function setService($name, $service)
    {
        $this->services[$name] = $service;
        return $this;
    }

    /**
     * @param array $services
     * @return $this
     */
    public function setServices(array $services)
    {
        foreach ($services as $name => $service) {
            $this->setService($name, $service);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasService($name)
    {
        return isset($this->services[$name]);
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \InvalidArgumentException
     */
    public function getService($name)
    {
        if (!$this->hasService($name)) {
            return;
        }

        $service = $this->services[$name];

        if (is_string($service)) {
            if (!class_exists($service)) {
                throw new \InvalidArgumentException(sprintf('Service class %s not found', $service));
            }

            $service = new $service;
        } else if ($service instanceof \Closure) {
            $service = $service($this);
        } else {
            return $service;
        }

        if ($service instanceof ServiceInterface) {
            $service->setApplication($this);
        }

        if (method_exists($service, 'initialize')) {
            $service->initialize($this);
        }

        # load config
        foreach ($this->getConfigDirectories() as $directory) {
            $configFile = $directory . '/' . $name . '.php';
            $envConfigFile = $directory . '/' . $name . '.' . $this->getEnv() . '.php';

            $this->applyServiceConfig($service, $configFile);
            $this->applyServiceConfig($service, $envConfigFile);
        }

        $this->services[$name] = $service;
        return $service;
    }

    protected function applyServiceConfig($service, $configFile)
    {
        if (!file_exists($configFile)) {
            return;
        }

        $callback = function($configFile) {
            require $configFile;
        };

        $callback = $callback->bindTo($service, $service);
        $callback($configFile);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasShortcutMethod($name)
    {
        return isset($this->shortcutMethods[$name]);
    }

    /**
     * @param string $name
     * @param string $service
     * @param null $serviceMethod
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addShortcutMethod($name, $service, $serviceMethod = null)
    {
        if ($this->hasShortcutMethod($name)) {
            throw new \InvalidArgumentException(sprintf('Shortcut method %s already exists', $name));
        }

        return $this->setShortcutMethod($name, $service, $serviceMethod);
    }

    /**
     * @param array $shortcuts
     * @return $this
     */
    public function addShortcutMethods(array $shortcuts)
    {
        foreach ($shortcuts as $name => $service) {
            if (is_array($service)) {
                $args = $service;
                array_unshift($args, $name);
            } else {
                $args = [$name, $service];
            }
            call_user_func_array([$this, 'addShortcutMethod'], $args);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $service
     * @param string [$serviceMethod]
     * @return $this
     */
    public function setShortcutMethod($name, $service, $serviceMethod = null)
    {
        if (!$serviceMethod) {
            $serviceMethod = $name;
        }

        $this->shortcutMethods[$name] = [$service, $serviceMethod];
        return $this;
    }

    /**
     * @param array $shortcuts
     * @return $this
     */
    public function setShortcutMethods(array $shortcuts)
    {
        foreach ($shortcuts as $name => $service) {
            if (is_array($service)) {
                $args = $service;
                array_unshift($args, $name);
            } else {
                $args = [$name, $service];
            }
            call_user_func_array([$this, 'setShortcutMethod'], $args);
        }

        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasShortcutProperty($name)
    {
        return isset($this->shortcutProperties[$name]);
    }

    /**
     * @param string $name
     * @param string $service
     * @param string [$serviceProperty]
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function addShortcutProperty($name, $service, $serviceProperty = null)
    {
        if ($this->hasShortcutProperty($name)) {
            throw new \InvalidArgumentException(sprintf('Shortcut property %s already exists', $name));
        }

        return $this->setShortcutProperty($name, $service, $serviceProperty);
    }

    /**
     * @param array $shortcuts
     * @return $this
     */
    public function addShortcutProperties(array $shortcuts)
    {
        foreach ($shortcuts as $name => $service) {
            if (is_array($service)) {
                $args = $service;
                array_unshift($args, $name);
            } else {
                $args = [$name, $service];
            }
            call_user_func_array([$this, 'addShortcutProperty'], $args);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $service
     * @param string [$serviceProperty]
     * @return $this
     */
    public function setShortcutProperty($name, $service, $serviceProperty = null)
    {
        if (!$serviceProperty) {
            $serviceProperty = $name;
        }

        $this->shortcutProperties[$name] = [$service, $serviceProperty];
        return $this;
    }

    /**
     * @param array $shortcuts
     * @return $this
     */
    public function setShortcutProperties(array $shortcuts)
    {
        foreach ($shortcuts as $name => $service) {
            if (is_array($service)) {
                $args = $service;
                array_unshift($args, $name);
            } else {
                $args = [$name, $service];
            }
            call_user_func_array([$this, 'setShortcutProperty'], $args);
        }

        return $this;
    }

    /**
     * @param $url
     */
    public function redirectTo($url)
    {
        call_user_func_array([$this->response, 'redirect'], func_get_args());
        $this->response->send();
        $this->stop();
    }

    /**
     *
     */
    public function redirectBack()
    {
        $this->redirectTo($this->request->getReferrer());
    }

    /**
     * @return $this
     */
    public function stop()
    {
        $this->stopped = true;
        return $this;
    }

    /**
     * @throws NotFound
     */
    public function notFound()
    {
        throw new NotFound();
    }

    /**
     * @throws Pass
     */
    public function pass()
    {
        throw new Pass();
    }

    protected function before() {}
    protected function after() {}

    protected function boot()
    {
        # load config
        foreach ($this->getConfigDirectories() as $directory) {
            $configFile = $directory . '/application.php';

            if (file_exists($configFile)) {
                require $configFile;
            }

            $envConfigFile = $directory . '/application.' . $this->getEnv() . '.php';

            if (file_exists($envConfigFile)) {
                require $envConfigFile;
            }
        }
    }

    /**
     * @return $this|void
     */
    public function run()
    {
        if (false === $this->before()) {
            return $this;
        }

        $this->boot();

        if ($this->notify('run') === false) {
            return $this;
        }

        if ($this->stopped) {
            $this->response->send();
            $this->after();
            $this->notify('ran');
            return $this;
        }

        $request = $this->request;
        $method = $request->getMethod();
        $requestPath = $request->getPath();

        if ($this->requestBasePath) {
            $requestPath = substr($requestPath, strlen($this->requestBasePath))?: '/';
        }

        $found = false;
        while ($route = $this->router->matches($method, $requestPath)) {
            $callback = $route->getCallback();
            $callback = $callback->bindTo($this);

            try {
                $result = call_user_func_array($callback, $route->getParams());
                $this->response->setBody($result)->send();
                $found = true;
                break;
            } catch(Pass $e) {

            }
        }

        $this->after();
        $this->notify('ran');

        if ($found) {
            return $this;
        }

        return $this->notFound();
    }
}