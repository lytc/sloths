<?php

namespace Lazy\Application;

use Lazy\Application\Exception\Pass;
use Lazy\Application\Exception\NotFound;
use Lazy\Config\ConfigurableTrait;
use Lazy\Http\Request;
use Lazy\Http\Response;
use Lazy\Routing\Route;
use Lazy\View\View;
use \Closure;

class Application
{
    use ConfigurableTrait;

    protected $applicationPath;
    protected $requestBasePath;
    protected $autoloadRouteCallback;
    protected $response;
    protected $routes = [];
    protected $services = [];

    /**
     * @var Request
     */
    protected $request;

    /**
     * @param string $requestBasePath
     */
    public function __construct($requestBasePath = '')
    {
        $this->requestBasePath = $requestBasePath;
    }

    /**
     * @param string $name
     * @param mixed $service
     * @return $this
     */
    public function register($name, $service)
    {
        $this->services[$name] = $service;
        return $this;
    }

    public function __get($serviceName)
    {
        switch ($serviceName) {
            case 'request': return $this->getRequest();
            case 'response': return $this->getResponse();
            case 'config': return $this->getConfig();
            case 'params': return $this->getRequest()->params;
        }

        if (!isset($this->services[$serviceName])) {
            throw new \InvalidArgumentException(sprintf('Call to undefined service %s', $serviceName));
        }

        $service = $this->services[$serviceName];

        if ($service instanceof Closure) {
            $service = call_user_func($service, $this);
            $this->services[$serviceName] = $service;
        }

        return $service;
    }

    public function setPath($path)
    {
        $this->applicationPath = $path;
        return $this;
    }

    public function getPath()
    {
        if (!$this->applicationPath) {
            $reflection = new \ReflectionClass(get_called_class());
            $classFile = $reflection->getFileName();
            $this->applicationPath = pathinfo($classFile, PATHINFO_DIRNAME);
        }

        return $this->applicationPath;
    }

    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * ->with('GET /foo', function() {})
     * ->with('GET POST /foo', function() {})
     * ->with(['GET', 'POST'], '/foo', function() {})
     *
     * @param string|array $method
     * @param string|callable [$pattern]
     * @param callable [$callback]
     * @return $this
     */
    public function map($method, $pattern, $callback = null) {
        if (!$callback) {
            $callback = $pattern;
            $pattern = $method;
            preg_match('/([\w ]+) \/(.*)/', $pattern, $matches);
            $method = $matches[1];
            $pattern = '/' . $matches[2];
        }

        $this->routes[] = new Route($method, $pattern, $callback);

        return $this;
    }

    /**
     * @param string $method
     * @param array $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (in_array($method, ['head', 'get', 'post', 'put', 'patch', 'delete', 'options', 'trace'])) {
            array_unshift($args, strtoupper($method));
            return call_user_func_array([$this, 'map'], $args);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s', $method));
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function setRequest(Request $request)
    {
        $this->request = $request;
        return $this;
    }

    /**
     * @return Request
     */
    public function getRequest()
    {
        if (!$this->request) {
            $this->request = new Request();
        }

        return $this->request;
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function param($name)
    {
        return $this->getRequest()->param($name);
    }

    /**
     * @return bool
     */
    public function isXhr()
    {
        return $this->getRequest()->isXhr();
    }

    /**
     * @param Response $response
     * @return $this
     */
    public function setResponse(Response $response)
    {
        $this->response = $response;
        return $this;
    }

    /**
     * @return Response
     */
    public function getResponse()
    {
        if (!$this->response) {
            $this->response = new Response();
        }

        return $this->response;
    }

    /**
     * @param $statusCode
     * @return $this
     */
    public function responseCode($statusCode)
    {
        $this->getResponse()->setStatusCode($statusCode);
        return $this;
    }

    /**
     * @param string $name
     * @param string $value
     * @return $this
     */
    public function responseHeader($name, $value = null)
    {
        $this->getResponse()->setHeader($name, $value);
        return $this;
    }

    /**
     * @param array $headers
     * @return $this
     */
    public function responseHeaders(array $headers)
    {
        $this->getResponse()->setHeaders($headers);
        return $this;
    }

    /**
     * @return \Lazy\View\View
     */
    public function getView()
    {
        if (!isset($this->services['view'])) {
            $this->services['view'] = new View();
        }

        return $this->view;
    }

    /**
     * @param $url
     */
    public function redirectTo($url)
    {
        call_user_func_array([$this->getResponse(), 'redirect'], func_get_args());
        return $this;
    }

    /**
     *
     */
    public function redirectBack()
    {
        $this->getResponse()->redirect($this->getRequest()->getReferrer());
    }

    /**
     * @param string $file
     * @param array [$variables]
     * @return string
     */
    public function render($file = null, array $variables = [])
    {
        return $this->getView()->render($file, $variables);
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

    /**
     * @param string $dispatchPath
     * @return string
     */
    protected function loadRoute($dispatchPath)
    {
        $routePath = $this->getPath() . '/routes';
        $routeFile = null;

        if ($dispatchPath && $dispatchPath != '/') {
            $parts = explode('/', $dispatchPath);

            if (file_exists($file = $routePath . '/' . $parts[1] . '.php')) {
                $routeFile = $file;
                $dispatchPath = '/' . implode('/', array_slice($parts, 2));
            }
        }

        if (!$routeFile) {
            $routeFile = $routePath . '/index.php';
        }

        if (file_exists($routeFile)) {
            require $routeFile;
        }

        return $dispatchPath;
    }

    /**
     * @return \Lazy\Routing\Route|null
     */
    public function getMatchedRoute()
    {
        $requestMethod  = $this->getRequest()->getMethod();
        $dispatchPath   = $this->getRequest()->getPath();


        if ($requestBasePath = $this->requestBasePath) {
            $dispatchPath = substr($dispatchPath, strlen($this->requestBasePath));
        }

        $dispatchPath = $this->loadRoute($dispatchPath);

        foreach ($this->routes as $route) {
            if (is_array($route->match($requestMethod, $dispatchPath))) {
                return $route;
            }
        }
    }

    /**
     * @param Request $request
     * @return $this
     */
    public function response(Request $request = null)
    {
        !$request || $this->request = $request;

        $matchedRoute = $this->getMatchedRoute();

        if ($matchedRoute) {
            $callback = $matchedRoute->getCallback();

            $result = call_user_func_array($callback, $matchedRoute->getParams());
            $this->getResponse()->setBody($result)->send();
            return $this;
        }

        return $this->notFound();
    }
}