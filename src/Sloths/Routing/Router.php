<?php

namespace Sloths\Routing;

class Router
{
    const HEAD      = 'HEAD';
    const GET       = 'GET';
    const POST      = 'POST';
    const PUT       = 'PUT';
    const PATCH     = 'PATCH';
    const DELETE    = 'DELETE';
    const OPTIONS   = 'OPTIONS';
    const TRACE     = 'TRACE';

    /**
     * @var string
     */
    protected $directory;

    /**
     * @var string
     */
    protected $basePath;

    /**
     * @var mixed
     */
    protected $context;

    /**
     * @var array
     */
    protected $routes = [];

    /**
     * @var int
     */
    protected $position = 0;

    public function __construct()
    {

    }

    /**
     * @param string $directory
     * @return $this
     */
    public function setDirectory($directory)
    {
        $this->directory = $directory;
        return $this;
    }

    /**
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * @param mixed $context
     * @return $this
     */
    public function setContext($context)
    {
        $this->context = $context;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getContext()
    {
        return $this->context;
    }

    /**
     * @return array
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * ->map('GET /foo', function() {})
     * ->map('GET POST /foo', function() {})
     * ->map(['GET', 'POST'], '/foo', function() {})
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

    public function head($pattern, \Closure $callback)
    {
        return $this->map(self::HEAD, $pattern, $callback);
    }

    public function get($pattern, \Closure $callback)
    {
        return $this->map(self::GET, $pattern, $callback);
    }

    public function post($pattern, \Closure $callback)
    {
        return $this->map(self::POST, $pattern, $callback);
    }

    public function put($pattern, \Closure $callback)
    {
        return $this->map(self::PUT, $pattern, $callback);
    }

    public function patch($pattern, \Closure $callback)
    {
        return $this->map(self::PATCH, $pattern, $callback);
    }

    public function delete($pattern, \Closure $callback)
    {
        return $this->map(self::DELETE, $pattern, $callback);
    }

    public function options($pattern, \Closure $callback)
    {
        return $this->map(self::OPTIONS, $pattern, $callback);
    }

    public function trace($pattern, \Closure $callback)
    {
        return $this->map(self::TRACE, $pattern, $callback);
    }

    /**
     * @param string $dispatchPath
     * @return string
     */
    protected function loadRouteFromFile($dispatchPath)
    {
        $routeFile = null;

        if ($dispatchPath && $dispatchPath != '/') {
            $parts = explode('/', trim($dispatchPath, '/'));

            if (preg_match('/[\w_\-]/', $parts[0]) && file_exists($file = $this->directory . '/' . $parts[0] . '.php')) {
                $routeFile = $file;
                $dispatchPath = '/' . implode('/', array_slice($parts, 1));
            }
        }

        if (!$routeFile) {
            $routeFile = $this->directory . '/index.php';
        }

        if (file_exists($routeFile)) {
            if ($this->context) {
                call_user_func(\Closure::bind(function() use ($routeFile) {
                    require $routeFile;
                }, $this->context));
            } else {
                require $routeFile;
            }
        }

        return $dispatchPath;
    }

    /**
     * @param string $requestMethod
     * @param string $dispatchPath
     * @return Route|null
     */
    public function matches($requestMethod, $dispatchPath)
    {
        $dispatchPath = $this->loadRouteFromFile($dispatchPath);

        for ($i = $this->position, $count = count($this->routes); $i < $count; $i++) {
            $route = $this->routes[$i];
            if (is_array($route->match($requestMethod, $dispatchPath))) {
                $this->position = $i;
                return $route;
            }
        }
    }

}