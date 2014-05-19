<?php

namespace SlothsTest\Routing;

use Sloths\Routing\Router;
use SlothsTest\TestCase;
use Sloths\Application\Application;

class RouterTest extends TestCase
{
    public function testGetAndSetDirectory()
    {
        $router = new Router();
        $router->setDirectory('foo');
        $this->assertSame('foo', $router->getDirectory());
    }

    public function testGetAndSetContext()
    {
        $context = new \stdClass();
        $router = new Router();
        $router->setContext($context);

        $this->assertSame($context, $router->getContext());
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

    public function test()
    {
        $callback = function() {};
        $router = $this->getMock('Sloths\Routing\Router', ['map']);
        $router->expects($this->at(0))->method('map')->with($router::HEAD, '/head', $callback);
        $router->expects($this->at(1))->method('map')->with($router::GET, '/get', $callback);
        $router->expects($this->at(2))->method('map')->with($router::POST, '/post', $callback);
        $router->expects($this->at(3))->method('map')->with($router::PUT, '/put', $callback);
        $router->expects($this->at(4))->method('map')->with($router::PATCH, '/patch', $callback);
        $router->expects($this->at(5))->method('map')->with($router::DELETE, '/delete', $callback);
        $router->expects($this->at(6))->method('map')->with($router::OPTIONS, '/options', $callback);
        $router->expects($this->at(7))->method('map')->with($router::TRACE, '/trace', $callback);

        $router->head('/head', $callback);
        $router->get('/get', $callback);
        $router->post('/post', $callback);
        $router->put('/put', $callback);
        $router->patch('/patch', $callback);
        $router->delete('/delete', $callback);
        $router->options('/options', $callback);
        $router->trace('/trace', $callback);
    }
}