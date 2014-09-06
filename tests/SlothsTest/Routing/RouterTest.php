<?php

namespace SlothsTest\Routing;

use Sloths\Routing\Router;
use Sloths\Routing\Route;
use SlothsTest\TestCase;
use Sloths\Http\RequestInterface;

class RouterTest extends TestCase
{
    public function testAdd()
    {
        $router = new Router();

        $route = new Route('GET', '/', function() {});
        $router->add($route);

        $this->assertSame($route, $router->getRoutes()[0]);
    }

    public function testMap()
    {
        $router = new Router();
        $router->map(RequestInterface::METHOD_POST, 'pattern', $callback = function() {});

        $route = $router->getRoutes()[0];

        $this->assertSame([ RequestInterface::METHOD_POST], $route->getMethods());
        $this->assertSame('pattern', $route->getPattern());
        $this->assertSame($callback, $route->getCallback());
    }

    /**
     * @dataProvider dataProviderTestShorthandMethods
     */
    public function testShorthandMethods($method, $requestMethod, $pattern, $callback)
    {
        $router = $this->getMock('Sloths\Routing\Router', ['map']);
        $router->expects($this->once())->method('map')->with($requestMethod, $pattern, $callback);
        $router->$method($pattern, $callback);
    }

    public function dataProviderTestShorthandMethods()
    {
        return [
            ['head', RequestInterface::METHOD_HEAD, 'pattern', function() {}],
            ['get', RequestInterface::METHOD_GET, 'pattern', function() {}],
            ['post', RequestInterface::METHOD_POST, 'pattern', function() {}],
            ['put', RequestInterface::METHOD_PUT, 'pattern', function() {}],
            ['patch', RequestInterface::METHOD_PATCH, 'pattern', function() {}],
            ['delete', RequestInterface::METHOD_DELETE, 'pattern', function() {}],
            ['options', RequestInterface::METHOD_OPTIONS, 'pattern', function() {}],
            ['trace', RequestInterface::METHOD_TRACE, 'pattern', function() {}],
            ['connect', RequestInterface::METHOD_CONNECT, 'pattern', function() {}],
        ];
    }
}