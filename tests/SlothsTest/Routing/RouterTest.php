<?php

namespace SlothsTest\Routing;

use Sloths\Routing\Router;
use SlothsTest\TestCase;
use Sloths\Application\Application;

/**
 * @covers \Sloths\Routing\Router
 */
class RouterTest extends TestCase
{
    public function testGetAndSetDirectory()
    {
        $router = new Router();
        $router->setDirectory('foo');
        $this->assertSame('foo', $router->getDirectory());
    }

    public function testContext()
    {
        $application = new Application();
        $application->setDirectory(__DIR__ . '/fixtures');
        $router = $application->router;
        $this->assertSame($application, $router->getContext());

        $route = $router->matches('GET', '/context-test');
        $this->assertSame($application, call_user_func($route->getCallback()));
    }

    public function testMap()
    {
        $router = new Router();
        $callback = function() {};
        $router->map('GET', '/foo', $callback);

        $this->assertCount(1, $router->getRoutes());
        $route = $router->getRoutes()[0];

        $this->assertSame(['GET' => 'GET'], $route->getMethod());
        $this->assertSame('/foo', $route->getPattern());
        $this->assertSame($callback, $route->getCallback());

        $callback2 = function() {};
        $router->map('POST /bar', $callback2);

        $this->assertCount(2, $router->getRoutes());
        $route = $router->getRoutes()[1];

        $this->assertSame(['POST' => 'POST'], $route->getMethod());
        $this->assertSame('/bar', $route->getPattern());
        $this->assertSame($callback2, $route->getCallback());
    }

    public function testLoadRoutesFromFile()
    {
        $router = new Router();
        $router->setDirectory(__DIR__ . '/fixtures/routes');

        $route = $router->matches('GET', '/foo/bar');
        $this->assertSame(['GET' => 'GET'], $route->getMethod());
        $this->assertSame('/bar', $route->getPattern());

        $this->assertCount(2, $router->getRoutes());

    }

    /**
     * @dataProvider dataProvider
     */
    public function testMethods($method, $requestMethod, $requestPath)
    {
        $callback = function() {};
        $router = $this->getMock('Sloths\Routing\Router', ['map']);
        $router->expects($this->once())->method('map')->with($requestMethod, $requestPath, $callback);

        $router->{$method}($requestPath, $callback);
    }

    public function dataProvider()
    {
        return [
            ['head', 'HEAD', '/head'],
            ['get', 'GET', '/get'],
            ['post', 'POST', '/post'],
            ['put', 'PUT', '/put'],
            ['patch', 'PATCH', '/patch'],
            ['delete', 'DELETE', '/delete'],
            ['options', 'OPTIONS', '/options'],
            ['trace', 'TRACE', '/trace'],
        ];
    }

    public function testDefaultRouteFile()
    {
        $router = new Router();
        $router->setDirectory(__DIR__ . '/fixtures/routes');
        $this->assertSame('index.php', $router->getDefaultRouteFile());
        $route = $router->matches('GET', '/');
        $this->assertSame(1, call_user_func($route->getCallback()));

        $router = new Router();
        $router->setDirectory(__DIR__ . '/fixtures/routes');
        $router->setDefaultRouteFile('default-routes.php');
        $this->assertSame('default-routes.php', $router->getDefaultRouteFile());
        $route =$router->matches('GET', '/');
        $this->assertSame(2, call_user_func($route->getCallback()));

        $router = new Router();
        $router->setDefaultRouteFile(__DIR__ . '/fixtures/routes/default-routes.php');
        $route =$router->matches('GET', '/');
        $this->assertSame(2, call_user_func($route->getCallback()));
    }
}