<?php

namespace Lazy\Application;

use Lazy\Application\Route\Exception\Pass;

class Router
{
    protected $routes = [];
    protected $routeInstance;

    public function add(Route $route)
    {
        $this->routes[] = $route;
    }

    public function map($method, $pattern, callable $callback, array $conditions = [])
    {
        $this->routes[] = [
            'method'    => $method,
            'pattern'   => $pattern,
            'callback'  => $callback,
            'conditions' => $conditions
        ];

        return $this;
    }

    public function get()
    {
        $args = func_get_args();
        array_unshift($args, 'GET');
        return call_user_func_array([$this, 'map'], $args);
    }

    public function post()
    {
        $args = func_get_args();
        array_unshift($args, 'POST');
        return call_user_func_array([$this, 'map'], $args);
    }

    public function put()
    {
        $args = func_get_args();
        array_unshift($args, 'PUT');
        return call_user_func_array([$this, 'map'], $args);
    }

    public function delete()
    {
        $args = func_get_args();
        array_unshift($args, 'DELETE');
        return call_user_func_array([$this, 'map'], $args);
    }

    public function trace()
    {
        $args = func_get_args();
        array_unshift($args, 'TRACE');
        return call_user_func_array([$this, 'map'], $args);
    }

    public function patch()
    {
        $args = func_get_args();
        array_unshift($args, 'PATCH');
        return call_user_func_array([$this, 'map'], $args);
    }

    public function matches($method, $uri)
    {
        $routes = [];
        foreach ($this->routes as $route) {
            if ($route->matches($method, $uri)) {
                $routes[] = $route;
            }
        }

        return $routes;
    }

    public function dispatch($method, $uri)
    {
        foreach ($this->routes as $route) {
            if (!($route instanceof Route)) {
                if (!$this->routeInstance) {
                    $this->routeInstance = new Route();
                }

                $params = $this->routeInstance->method(true, $route['method'])
                    ->pattern($route['pattern'])
                    ->conditions(true, $route['conditions'])
                    ->matches($method, $uri);
            } else {
                $params = $route->matches($method, $uri);
            }

            if (is_array($params)) {
                try {
                    $result = call_user_func_array($route['callback'], $params);
                    if (is_array($result) || is_object($result)) {
                        echo json_encode($result);
                    }
                    return true;
                } catch (Pass $e) {
                    continue;
                }
            }
        }
    }
}