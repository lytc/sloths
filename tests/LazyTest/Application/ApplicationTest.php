<?php

namespace LazyTest\Application;
use Lazy\Application\Application;
use Lazy\Http\Request;
use Lazy\Http\Response;
use Lazy\View\View;
use LazyTest\TestCase;

class ApplicationTest extends TestCase
{
    public function testGetAndSetRequest()
    {
        $application = new Application();
        $this->assertInstanceOf('Lazy\Http\Request', $application->getRequest());

        $request = new Request();
        $application->setRequest($request);
        $this->assertSame($request, $application->getRequest());
    }

    public function testGetAndSetResponse()
    {
        $application = new Application();
        $this->assertInstanceOf('Lazy\Http\Response', $application->getResponse());

        $response = new Response();
        $application->setResponse($response);
        $this->assertSame($response, $application->getResponse());
    }

//    public function testGetAndSetView()
//    {
//        $application = new Application();
//        $this->assertInstanceOf('Lazy\View\View', $application->getView());
//
//        $view = new View();
//        $application->setView($view);
//        $this->assertSame($view, $application->getView());
//    }
//
//    public function testGetViewWithConfig()
//    {
//        $application = new Application();
//        $application->setPath(__DIR__ . '/fixtures');
//        $view = $application->getView();
//
//        $config = require __DIR__ . '/fixtures/config/view.php';
//        $this->assertSame($config['path'], $view->getPath());
//        $this->assertSame($config['layout'], $view->getLayout());
//    }

    public function testRedirectTo()
    {
        $application = new Application();
        $application->redirectTo('foo');
        $this->assertSame(403, $application->getResponse()->getStatusCode());
        $this->assertSame('foo', $application->getResponse()->getHeader('Location'));
    }

    public function testRedirectBack()
    {
        $application = new Application();
        $request = new Request([
            '_SERVER' => ['HTTP_REFERER' => 'foo']
        ]);
        $application->setRequest($request);

        $application->redirectBack();
        $this->assertSame('foo', $application->getResponse()->getHeader('Location'));
    }

    public function testRender()
    {
        $view = $this->mock('Lazy\View\View');
        $view->shouldReceive('render')->once()->with('foo', ['foo' => 'bar'])->andReturn('foo');

        $application = new Application();
        $application->register('view', $view);

        $this->assertSame('foo', $application->render('foo', ['foo' => 'bar']));
    }

    public function testMap()
    {
        $application = new Application();
        $callback = function() {};
        $application->map('GET', '/foo', $callback);

        $this->assertCount(1, $application->getRoutes());
        $route = $application->getRoutes()[0];

        $this->assertSame(['GET' => 'GET'], $route->getMethod());
        $this->assertSame('/foo', $route->getPattern());
        $this->assertSame($callback, $route->getCallback());

        $callback2 = function() {};
        $application->map('POST /bar', $callback2);

        $this->assertCount(2, $application->getRoutes());
        $route = $application->getRoutes()[1];

        $this->assertSame(['POST' => 'POST'], $route->getMethod());
        $this->assertSame('/bar', $route->getPattern());
        $this->assertSame($callback2, $route->getCallback());

    }

    public function test()
    {
        $this->expectOutputString('foo');

        $application = new Application();

        $application->get('/foo', function() {
            return 'foo';
        });


        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/foo']
        ]);
        $application->response($request);
    }

    public function testWithRequestBasePath()
    {
        $this->expectOutputString('foo');

        $application = new Application('/bar');

        $application->get('/foo', function() {
            return 'foo';
        });


        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/bar/foo']
        ]);
        $application->response($request);
    }

    public function testLoadRoutesFromFile()
    {
        $application = new Application();
        $application->setPath(__DIR__ . '/fixtures');

        $request = new Request([
            '_SERVER' => [
                'REQUEST_METHOD' => 'GET',
                'PATH_INFO' => '/foo/bar'
            ]
        ]);

        $application->setRequest($request);

        $matchedRoute = $application->getMatchedRoute();
        $this->assertSame(['GET' => 'GET'], $matchedRoute->getMethod());
        $this->assertSame('/bar', $matchedRoute->getPattern());

        $this->assertCount(2, $application->getRoutes());

    }

    /**
     * @expectedException \Lazy\Application\Exception\NotFound
     */
    public function testNotFound()
    {
        $application = new Application();

        $application->get('/bar', function() {
            return 'foo';
        });


        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/foo']
        ]);
        $application->response($request);
    }

    /**
     * @expectedException Lazy\Application\Exception\Pass
     */
    public function testMethodPass()
    {
        $application = new Application();
        $application->pass();
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallShouldThrowException()
    {
        $application = new Application();
        $application->badMethod();
    }
}