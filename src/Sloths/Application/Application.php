<?php

namespace Sloths\Application;

use Sloths\Application\Exception\AccessDenied;
use Sloths\Application\Exception\Error;
use Sloths\Application\Exception\NotFound;
use Sloths\Application\Service\ServiceManager;
use Sloths\Http\Request;
use Sloths\Http\RequestInterface;
use Sloths\Http\Response;
use Sloths\Http\ResponseInterface;
use Sloths\Misc\ConfigurableTrait;
use Sloths\Misc\DynamicMethodTrait;
use Sloths\Misc\DynamicPropertyTrait;
use Sloths\Misc\Parameters;
use Sloths\Observer\ObserverTrait;
use Sloths\Routing\Router;
use Sloths\Application\Exception\Pass;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Application implements ApplicationInterface
{
    use ObserverTrait;
    use DynamicMethodTrait;
    use DynamicPropertyTrait;
    use ConfigurableTrait;

    const DEFAULT_ENV = 'production';

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var callable
     */
    protected $resourceDirectory;

    /**
     * @var string
     */
    protected $env;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $baseUrl = '/';

    /**
     * @var array
     */
    protected $paths = [];

    /**
     * @var RequestInterface
     */
    protected $request;

    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * @var Router
     */
    protected $router;

    /**
     * @var ServiceManager
     */
    protected $serviceManager;

    /**
     * @var ConfigLoader
     */
    protected $configLoader;

    /**
     * @var string
     */
    protected $defaultRouteGroupName = 'index';

    /**
     * @var ModuleManager
     */
    protected $moduleManager;

    /**
     * @var bool
     */
    protected $booted = false;

    /**
     * @param ModuleManager $moduleManager
     */
    public function __construct(ModuleManager $moduleManager = null)
    {
        $this->moduleManager = $moduleManager;
    }

    /**
     * @return ModuleManager
     */
    public function getModuleManager()
    {
        return $this->moduleManager;
    }

    /**
     * @param string $name
     * @return mixed|ServiceInterface
     */
    public function __get($name)
    {
        switch ($name) {
            case 'request':     return $this->getRequest();
            case 'response':    return $this->getResponse();
            case 'router':      return $this->getRouter();
            case 'params':      return $this->getRequest()->getParams();
            case 'paramsQuery': return $this->getRequest()->getParamsQuery();
            case 'paramsPost':  return $this->getRequest()->getParamsPost();
            case 'paramsFile':  return $this->getRequest()->getParamsFile();
        }

        $serviceManager = $this->getServiceManager();

        if ($serviceManager->has($name)) {
            return $serviceManager->get($name);
        }

        return $this->getDynamicProperty($name);
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     */
    public function __call($method, $args)
    {
        if (in_array($method, ['head', 'get', 'post', 'put', 'patch', 'delete', 'options', 'trace', 'connect'])) {
            return call_user_func_array([$this->getRouter(), $method], $args);
        }

        return $this->callDynamicMethod($method, $args);
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
     * @param string $baseUrl
     * @return $this
     */
    public function setBaseUrl($baseUrl)
    {
        $baseUrl = rtrim($baseUrl, '/')?: '/';
        $this->baseUrl = $baseUrl;

        return $this;
    }

    /**
     * @param bool $full
     * @return string
     */
    public function getBaseUrl($full = true)
    {
        $baseUrl = $this->baseUrl;
        if ($full) {
            if (!preg_match('/^https?::/', $baseUrl)) {
                $baseUrl = $this->getRequest()->getBaseUrl() . $baseUrl;
            }
        } else {
            if (preg_match('/^https?::/', $baseUrl)) {
                $baseUrl = parse_url($baseUrl, PHP_URL_PATH);
            }
        }

        return rtrim($baseUrl, '/');
    }

    /**
     * @param string $directory
     * @throws \InvalidArgumentException
     * @return $this
     */
    public function setDirectory($directory)
    {
        $directory = realpath($directory);

        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('Invalid directory: ' . $directory);
        }

        $this->directory = $directory;

        return $this;
    }

    /**
     * @param string $directory
     * @return $this
     */
    public function setResourceDirectory($directory)
    {
        $this->resourceDirectory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getResourceDirectory()
    {
        return $this->resourceDirectory;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param array $paths
     * @return $this
     */
    public function setPaths(array $paths)
    {
        foreach ($paths as $name => $path) {
            $this->setPath($name, $path);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param string $path
     */
    public function setPath($name, $path)
    {
        if ('/' !== $path[0]) {
            $path = $this->getDirectory() . '/' . $path;
        }

        $this->paths[$name] = realpath($path);
    }

    /**
     * @param string $name
     * @return string|null
     */
    public function getPath($name)
    {
        if (isset($this->paths[$name])) {
            return $this->paths[$name];
        }

        return $this->getDirectory() . '/' . $name;
    }

    public function getResourcePath($name)
    {
        return $this->getDirectory() . '/' . $this->getResourceDirectory() . '/' . $name;
    }

    /**
     * @param \Sloths\Http\RequestInterface $request
     * @return $this
     */
    public function setRequest(RequestInterface $request)
    {
        $this->request = $request;
    }

    /**
     * @return \Sloths\Http\RequestInterface
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param \Sloths\Http\ResponseInterface $response
     * @return $this
     */
    public function setResponse(ResponseInterface $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return \Sloths\Http\ResponseInterface
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new Response();
        }

        return $this->response;
    }

    /**
     * @param Router $router
     * @return $this
     */
    public function setRouter(Router $router)
    {
        $this->router = $router;
        return $this;
    }

    /**
     * @return Router
     */
    public function getRouter()
    {
        if (!$this->router) {
            $this->router = new Router();
        }

        return $this->router;
    }

    /**
     * @param ServiceManager $manager
     * @return $this
     */
    public function setServiceManager(ServiceManager $manager)
    {
        $this->serviceManager = $manager;
        return $this;
    }

    /**
     * @return ServiceManager
     */
    public function getServiceManager()
    {
        if (!$this->serviceManager) {
            $this->serviceManager = new ServiceManager($this);
//            $this->boot();
        }

        return $this->serviceManager;
    }

    /**
     * @param ConfigLoader $configLoader
     * @return $this
     */
    public function setConfigLoader(ConfigLoader $configLoader)
    {
        $this->configLoader = $configLoader;
        return $this;
    }

    /**
     * @return ConfigLoader
     */
    public function getConfigLoader()
    {
        if (!$this->configLoader) {
            $this->configLoader = new ConfigLoader($this);
        }

        return $this->configLoader;
    }

    /**
     * @throws Exception\NotFound
     */
    public function notFound()
    {
        throw new NotFound();
    }

    /**
     * @param string $message
     * @param int $code
     * @throws Error
     */
    public function error($message = '', $code = 500)
    {
        throw new Error($message, $code);
    }

    /**
     * @param string $message
     * @param int $code
     * @throws Exception\AccessDenied
     */
    public function accessDenied($message = 'Access denied', $code = 403)
    {
        throw new AccessDenied($message, $code);
    }

    /**
     * @throws Exception\Pass
     */
    public function pass()
    {
        throw new Pass();
    }

    /**
     * @param string|RequestInterface $request
     * @param array $params
     * @return RequestInterface
     */
    public function forward($request, array $params = [])
    {
        if (!$request instanceof RequestInterface) {
            $parts = explode(' ', $request, 2);

            if (count($parts) == 1) {
                $this->getRequest()->setPath($parts[0]);
            } else {
                $this->getRequest()->setMethod($parts[0])->setPath($parts[1]);
            }

            if ($params) {
                $params = array_merge($this->getRequest()->getParams()->toArray(), $params);
                $this->getRequest()->setParams(new Parameters($params));
            }

            $request = $this->getRequest();

        }

        return $request;
    }

    /**
     *
     */
    public function boot()
    {
        if (!$this->booted) {
            $this->triggerEventListener('boot', [$this]);

            $this->getConfigLoader()->addDirectories($this->getResourcePath('config'));

            if (!($env = $this->getEnv())) {
                $this->setEnv(getenv('SLOTHS_ENV')?: static::DEFAULT_ENV);
            }

            $this->getConfigLoader()->apply('application', $this);

            $this->loadRoutes();

            $this->booted = true;
            $this->triggerEventListener('booted', [$this]);
        }
    }

    protected function loadRoutes()
    {
        $routesPath = $this->getResourcePath('routes');

        $finder = new Finder();
        $files = $finder->in($routesPath)->files()->name('*.php');

        $files->sort(function(SplFileInfo $a, SplFileInfo $b) {
            return strlen($a->getRelativePath()) > strlen($b->getRelativePath());
        });

        foreach ($files as $file) {
            /* @var $file \Symfony\Component\Finder\SplFileInfo */
            $basePath = '/' . substr($file->getRelativePathname(), 0, -4);
            $this->getRouter()->setBasePath($this->getBaseUrl(false) . ($basePath != '/' . $this->defaultRouteGroupName? $basePath : '/'));

            require $file->getRealPath();
        }
    }

    /**
     * @return $this
     */
    public function send()
    {
        $this->triggerEventListener('send', [$this]);

        $response = $this->getResponse();

        $headers = $response->getHeaders();
        $body = $this->getResponse()->getBody();

        if (is_array($body) || $body instanceof \JsonSerializable) {
            $body = json_encode($body);
            if (!$headers->has('Content-Type')) {
                $headers->set('Content-Type', 'application/json');
            }
        }

        if (!$this->getResponse()->getStatusCode()) {
            $this->getResponse()->setStatusCode(200);
        }

        # first header line
        $line = sprintf('HTTP/%s %d %s', $response->getProtocolVersion(),
            $response->getStatusCode(), $response->getReasonPhrase());

        $this->sendHeader($line);

        foreach ($headers->getLines() as $line) {
            $this->sendHeader($line);
        }

        echo $body;

        $this->triggerEventListener('sent', [$this]);

        return $this;
    }

    protected function sendHeader($headerLine)
    {
        if (PHP_SAPI != 'cli') {
            header($headerLine);
        }
    }

    protected function resolveRequest()
    {
        $method = $this->getRequest()->getMethod();
        $path = $this->getRequest()->getPath();

        foreach ($this->getRouter() as $route) {
            try {
                $params = $route->match($method, $path);
                if (!is_array($params)) {
                    continue;
                }

                $callback = $route->getCallback();

                $result = call_user_func_array($callback, $params);

                if ($result instanceof ResponseInterface) {
                    return $this->setResponse($result);
                }

                if ($result instanceof RequestInterface) {
                    $this->setRequest($result);
                    return $this->resolveRequest();
                }

                return $this->getResponse()->setBody($result);
            } catch (Pass $e) {

            }
        }

        return null !== $this->getResponse()->getStatusCode();
    }

    /**
     * @return $this
     */
    public function run()
    {
        $this->boot();

        if ($result = $this->triggerEventListener('before')) {
            if ($result != $this->getResponse()) {
                $this->getResponse()->setBody($result);
            }
        } else {
            if (false === $this->resolveRequest()) {
                $this->triggerEventListener('after');
                $this->notFound();
            }
        }

        $this->send();
        $this->triggerEventListener('after');
    }
}