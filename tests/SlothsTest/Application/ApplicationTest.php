<?php

namespace SlothsTest\Application;
use Sloths\Application\Application;
use Sloths\Application\Service\Request;
use SlothsTest\TestCase;

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

    public function testRedirectTo()
    {
        $response = $this->mock('Sloths\Application\Service\Response[redirect,send]');
        $response->shouldReceive('redirect')->once()->with('foo');
        $response->shouldReceive('send')->once();

        $application = $this->getMock('Sloths\Application\Application', ['stop']);
        $application->expects($this->once())->method('stop');
        $application->setService('response', $response);
        $application->redirectTo('foo');
    }

    public function testRedirectBack()
    {
        $response = $this->mock('\Sloths\Application\Service\Response[redirect,send]');
        $response->shouldReceive('redirect')->once()->with('foo');
        $response->shouldReceive('send')->once();

        $application = $this->getMock('Sloths\Application\Application', ['stop']);
        $application->expects($this->once())->method('stop');
        $application->setService('response', $response);

        $request = new Request([
            '_SERVER' => ['HTTP_REFERER' => 'foo']
        ]);
        $application->setService('request', $request);

        $application->redirectBack('foo');
    }

    public function testRun()
    {
        $response = $this->mock('Sloths\Application\Service\Response[setBody,send]');
        $response->shouldReceive('setBody')->once()->with('foo')->andReturnSelf();
        $response->shouldReceive('send')->once();

        $application = new Application();
        $application->setService('response', $response);

        $application->get('/foo', function() {
            return 'foo';
        });


        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/foo']
        ]);
        $application->setService('request', $request);
        $application->run();
    }

    public function testWithRequestBasePath()
    {
        $response = $this->mock('Sloths\Application\Service\Response[setBody,send]');
        $response->shouldReceive('setBody')->once()->with('foo')->andReturnSelf();
        $response->shouldReceive('send')->once();

        $application = new Application('/bar');
        $application->setService('response', $response);

        $application->get('/foo', function() {
            return 'foo';
        });


        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET', 'PATH_INFO' => '/bar/foo']
        ]);
        $application->setService('request', $request);
        $application->run($request);
    }

    /**
     * @expectedException \Sloths\Application\Exception\NotFound
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
        $application->run($request);
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
        $route1 = $this->mock('Sloths\Routing\Route');
        $route1->shouldReceive('getCallback')->once()->andReturn(function() {
            $this->pass();
        });

        $route1->shouldReceive('getParams')->once()->andReturn([]);

        $route2 = $this->mock('Sloths\Routing\Route');
        $route2->shouldReceive('getCallback')->once()->andReturn(function() use (&$output) {
            $output = 1;
        });
        $route2->shouldReceive('getParams')->once()->andReturn([]);

        $router = $this->getMock('Sloths\Application\Service\Router', ['matches']);
        $router->expects($this->at(0))->method('matches')->willReturn($route1);
        $router->expects($this->at(1))->method('matches')->willReturn($route2);

        $application = new Application();
        $application->setService('router', $router);


        $response = $this->mock('Sloths\Application\Service\Response[setBody,send]');
        $response->shouldReceive('setBody')->andReturnSelf();
        $response->shouldReceive('send');
        $application->setService('response', $response);

        $application->run();
        $this->assertSame(1, $output);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallShouldThrowException()
    {
        $application = new Application();
        $application->badMethod();
    }

    public function testBeforeMethodReturnsFalseShouldNotCallAfterMethod()
    {
        $application = $this->getMock('Sloths\Application\Application', ['before', 'after']);
        $application->expects($this->once())->method('before')->willReturn(false);
        $application->expects($this->never())->method('after');
        $application->run();
    }

    public function testBeforeMethodAndAfterMethod()
    {
        $application = $this->getMock('Sloths\Application\Application', ['before', 'after', 'notFound']);
        $application->expects($this->once())->method('before');
        $application->expects($this->once())->method('after');
        $application->expects($this->once())->method('notFound');
        $application->run();
    }

    public function testListenerRunAndRan()
    {
        $application = $this->getMock('Sloths\Application\Application', ['notFound']);
        $application->expects($this->once())->method('notFound');

        $application->addListener('run', function() use (&$runCalled) {
            $runCalled = true;
        });

        $application->addListener('ran', function() use (&$ranCalled) {
            $ranCalled = true;
        });

        $application->run();

        $this->assertTrue($runCalled);
        $this->assertTrue($ranCalled);
    }

    public function testListenerRunReturnsFalseShouldNotTriggerRan()
    {
        $application = new Application();

        $application->addListener('run', function() use (&$runCalled) {
            $runCalled = true;
            return false;
        });

        $ranCalled = false;
        $application->addListener('ran', function() use (&$ranCalled) {
            $ranCalled = true;
        });

        $application->run();

        $this->assertTrue($runCalled);
        $this->assertFalse($ranCalled);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUndefinedPropertyShouldThrowAnException()
    {
        $application = new Application();
        $application->non_existing_property;
    }
}