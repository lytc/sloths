<?php

namespace Sloths\Routing;

use Sloths\Http\RequestInterface;

class Router implements \IteratorAggregate
{
    /**
     * @var Route[]
     */
    protected $routes = [];

    public function __construct()
    {
    }

    /**
     * @return Route[]
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /**
     * @param Route $route
     * @return Route
     */
    public function add(Route $route) {
        $this->routes[] = $route;
        return $route;
    }

    /**
     * @param string|array $methods
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function map($methods, $pattern, callable $callback)
    {
        return $this->add(new Route($methods, $pattern, $callback));
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function head($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_HEAD, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function get($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_GET, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function post($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_POST, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function put($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_PUT, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function patch($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_PATCH, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function delete($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_DELETE, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function options($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_OPTIONS, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function trace($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_TRACE, $pattern, $callback);
    }

    /**
     * @param string $pattern
     * @param callable $callback
     * @return Route
     */
    public function connect($pattern, callable $callback)
    {
        return $this->map(RequestInterface::METHOD_CONNECT, $pattern, $callback);
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->getRoutes());
    }
}