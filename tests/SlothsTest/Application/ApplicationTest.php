<?php

namespace SlothsTest\Application;
use Sloths\Application\Application;
use Sloths\Http\Response;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Application\Application
 */
class ApplicationTest extends TestCase
{
    public function testEnvMethod()
    {
        $application = new Application();
        $this->assertSame('production', $application->getEnv());

        $application->setEnv('development');
        $this->assertSame('development', $application->getEnv());
    }

    public function testDebugMethod()
    {
        $application = new Application();
        $this->assertFalse($application->getDebug());

        $application->setDebug(true);
        $this->assertTrue($application->getDebug());
    }

    public function testGetAndSetDirectory()
    {
        $application = new Application();
        $application->setDirectory('foo');
        $this->assertSame('foo', $application->getDirectory());
    }

    public function testConfigDirectoryMethod()
    {
        $application = new Application();

        $applicationDirectory = $application->getDirectory();

        $expected = [$applicationDirectory . '/config' => $applicationDirectory . '/config'];
        $this->assertSame($expected, $application->getConfigDirectories());

        $application->addConfigDirectory('foo');
        $expected['foo'] = 'foo';
        $this->assertSame($expected, $application->getConfigDirectories());

        $application->addConfigDirectories(['bar' => 'bar']);
        $expected['bar'] = 'bar';
        $this->assertSame($expected, $application->getConfigDirectories());
    }

    public function testRun()
    {
        $application = $this->getMock('Sloths\Application\Application', ['send']);
        $application->expects($this->once())->method('send');

        $application->get('/foo', function() {
            return 'foo';
        });
        $application->request->setServerVars(['REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/foo']);
        $application->run();

        $this->assertSame('foo', $application->response->getBody());
    }

    public function testWithRequestBasePath()
    {
        $application = $this->getMock('Sloths\Application\Application', ['send'], ['/bar']);
        $application->expects($this->once())->method('send');

        $application->get('/foo', function() {
            return 'foo';
        });
        $application->request->setServerVars(['REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/bar/foo']);
        $application->run();

        $this->assertSame('foo', $application->response->getBody());
    }

    public function testNotFound()
    {
        $application = $this->getMock('Sloths\Application\Application', ['send']);
        $application->run();
        $this->assertSame(404, $application->response->getStatusCode());
    }

    public function testCallNotFoundInRoute()
    {
        $route = $this->getMock('Sloths\Routing\Route', ['getParams', 'getCallback']);
        $route->expects($this->once())->method('getParams')->willReturn([]);
        $a = false;
        $b = false;

        $route->expects($this->once())->method('getCallback')->willReturn(function() use (&$a, &$b) {
            $a = true;
            $this->notFound();
            $b = true;
        });

        $router = $this->getMock('Sloths\Application\Service\Router', ['matches']);
        $router->expects($this->once())->method('matches')->willReturn($route);

        $application = $this->getMock('Sloths\Application\Application', ['send']);
        $application->setService('router', $router);

        $application->run();

        $this->assertTrue($a);
        $this->assertFalse($b);
        $this->assertSame(404, $application->response->getStatusCode());
    }

    /**
     * @expectedException \Sloths\Application\Exception\Pass
     */
    public function testMethodPass()
    {
        $application = new Application();
        $application->pass();
    }

    public function testPassToNextRoute()
    {
        $route1 = $this->getMock('Sloths\Routing\Route', ['getCallback', 'getParams']);
        $route1->expects($this->once())->method('getCallback')->willReturn(function() {
            $this->pass();
        });

        $route1->expects($this->once())->method('getParams')->willReturn([]);

        $route2 = $this->getMock('Sloths\Routing\Route', ['getCallback', 'getParams']);
        $route2->expects($this->once())->method('getCallback')->willReturn(function() {
            return 'foo';
        });
        $route2->expects($this->once())->method('getParams')->willReturn([]);

        $router = $this->getMock('Sloths\Application\Service\Router', ['matches']);
        $router->expects($this->at(0))->method('matches')->willReturn($route1);
        $router->expects($this->at(1))->method('matches')->willReturn($route2);

        $application = $this->getMock('Sloths\Application\Application', ['send']);
        $application->expects($this->once())->method('send');

        $application->setService('router', $router);

        $application->run();
        $this->assertSame('foo', $application->response->getBody());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallShouldThrowException()
    {
        $application = new Application();
        $application->badMethod();
    }

    public function testBeforeMethodReturnsResponseObjectShouldRunThatResponseObject()
    {
        $response = new Response();
        $application = $this->getMock('Sloths\Application\Application', ['before', 'send']);
        $application->expects($this->once())->method('before')->willReturn($response);
        $application->expects($this->once())->method('send');
        $application->run();
        $this->assertSame($response, $application->response);
    }

    public function testBeforeAndAfter()
    {
        $route = $this->getMock('Sloths\Routing\Route', ['getParams'], ['GET', '/', function() {}]);
        $route->expects($this->once())->method('getParams')->willReturn([]);

        $router = $this->getMock('Sloths\Routing\Router', ['matches', 'getParams']);
        $router->expects($this->once())->method('matches')->willReturn($route);

        $response = new Response();

        $application = $this->getMock('Sloths\Application\Application', ['before', 'after', 'notify', 'send']);
        $application->setService('router', $router);
        $application->setService('response', $response);

        $application->expects($this->once())->method('before');
        $application->expects($this->once())->method('send');
        $application->expects($this->once())->method('after');
        $application->expects($this->at(1))->method('notify')->with('run');
        $application->expects($this->at(4))->method('notify')->with('ran');
        $application->run();

        $this->assertSame($response, $application->response);
    }

    public function testListenerRunReturnsResponseObjectShouldRunThatResponse()
    {
        $response = new Response();
        $application = $this->getMock('Sloths\Application\Application', ['send']);
        $application->expects($this->once())->method('send');

        $application->addListener('run', function() use ($response) {
            return $response;
        });

        $application->run();
        $this->assertSame($response, $application->response);
    }

    public function testReturnResponseInRoute()
    {
        $response = new Response();
        $route = $this->getMock('Sloths\Routing\Route', ['getCallback', 'getParams']);
        $route->expects($this->once())->method('getCallback')->willReturn(function () use ($response) {
            return $response;
        });
        $route->expects($this->once())->method('getParams')->willReturn([]);

        $router = $this->getMock('Sloths\Routing\Router', ['matches']);
        $router->expects($this->once())->method('matches')->willReturn($route);

        $application = $this->getMock('Sloths\Application\Application', ['send']);
        $application->setService('router', $router);
        $application->run();

        $this->assertSame($response, $application->response);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUndefinedPropertyShouldThrowAnException()
    {
        $application = new Application();
        $application->non_existing_property;
    }

    /**
     * @runInSeparateProcess
     * @dataProvider dataProviderTestSend
     */
    public function testSend($expectedBody, $expectedHeaders, $response)
    {
        $this->expectOutputString($expectedBody);

        $route = $this->getMock('Sloths\Routing\Route', ['getCallback', 'getParams']);
        $route->expects($this->once())->method('getCallback')->willReturn(function () use ($response) {
            return $response;
        });
        $route->expects($this->once())->method('getParams')->willReturn([]);

        $router = $this->getMock('Sloths\Routing\Router', ['matches']);
        $router->expects($this->once())->method('matches')->willReturn($route);

        $application = new Application();
        $application->setService('router', $router);

        $application->run();

        if (!extension_loaded('xdebug')) {
            $this->markTestSkipped('Require xdebug extension for testing headers');
        }

        $this->assertSame($expectedHeaders, array_intersect($expectedHeaders, xdebug_get_headers()));
    }

    public function dataProviderTestSend()
    {
        return [
            ['foo', ['Foo: bar'], (new Response())->setBody('foo')->setHeaders(['Foo' => 'bar'])]
        ];
    }
}