<?php

namespace SlothsTest\Application;

use Sloths\Application\ConfigLoader;
use Sloths\Application\Exception\AccessDenied;
use Sloths\Application\Exception\Error;
use Sloths\Application\Service\ServiceManager;
use Sloths\Http\Request;
use Sloths\Http\Response;
use Sloths\Misc\Parameters;
use Sloths\Routing\Route;
use Sloths\Routing\Router;
use SlothsTest\TestCase;
use Sloths\Application\Application;

/**
 * @covers Sloths\Application\Application
 */
class ApplicationTest extends TestCase
{
    public function testDirectory()
    {
        $application = new Application();
        $dir = __DIR__ . '/../';
        $application->setDirectory($dir);
        $this->assertSame(realpath($dir), $application->getDirectory());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidDirectoryShouldThrowAnException()
    {
        $application = new Application();
        $application->setDirectory(__DIR__ . '/foo');
    }

    public function testSetPaths()
    {
        $application = $this->getMock('Sloths\Application\Application', ['setPath']);
        $application->expects($this->once())->method('setPath')->with('foo', 'bar');

        $application->setPaths(['foo' => 'bar']);
    }

    public function testPathWithAbsolutePath()
    {
        $dir = __DIR__;
        $application = new Application();
        $application->setPath('foo', $dir);
        $this->assertSame($dir, $application->getPath('foo'));
    }

    public function testPathWithRelativePath()
    {
        $applicationPath = __DIR__;
        $application = new Application();
        $application->setDirectory($applicationPath);

        $application->setPath('foo', 'Service');
        $this->assertSame($applicationPath . '/Service', $application->getPath('foo'));

        $this->assertSame($applicationPath . '/bar', $application->getPath('bar'));
    }

    public function testRequest()
    {
        $application = new Application();
        $this->assertInstanceOf('Sloths\Http\Request', $application->getRequest());
        $this->assertSame($application->getRequest(), $application->getRequest());

        $request = $this->getMock('Sloths\Http\RequestInterface');
        $application->setRequest($request);
        $this->assertSame($request, $application->getRequest());
    }

    public function testResponse()
    {
        $application = new Application();
        $this->assertInstanceOf('Sloths\Http\Response', $application->getResponse());
        $this->assertSame($application->getResponse(), $application->getResponse());

        $response = $this->getMock('Sloths\Http\ResponseInterface');
        $application->setResponse($response);
        $this->assertSame($response, $application->getResponse());
    }

    public function testRouter()
    {
        $application = new Application();
        $this->assertInstanceOf('Sloths\Routing\Router', $application->getRouter());
        $this->assertSame($application->getRouter(), $application->getRouter());

        $router = new Router();
        $application->setRouter($router);
        $this->assertSame($router, $application->getRouter());
    }

    public function testServiceManager()
    {
        $application = new Application();
        $this->assertInstanceOf('Sloths\Application\Service\ServiceManager', $application->getServiceManager());
        $this->assertSame($application->getServiceManager(), $application->getServiceManager());

        $serviceManager = new ServiceManager($application);
        $application->setServiceManager($serviceManager);
        $this->assertSame($serviceManager, $application->getServiceManager());
    }

    public function testConfigLoader()
    {
        $application = new Application();
        $this->assertInstanceOf('Sloths\Application\ConfigLoader', $application->getConfigLoader());
        $this->assertSame($application->getConfigLoader(), $application->getConfigLoader());

        $config = new ConfigLoader($application);
        $application->setConfigLoader($config);
        $this->assertSame($config, $application->getConfigLoader());
    }

    /**
     * @expectedException \Sloths\Application\Exception\NotFound
     */
    public function testNotFound()
    {
        $application = new Application();
        $application->notFound();
    }

    public function testError()
    {
        $application = new Application();

        try {
            $application->error('error', 501);
        } catch (Error $e) {

        }

        $this->assertSame(501, $e->getCode());
        $this->assertSame('error', $e->getMessage());
    }

    /**
     * @expectedException \Sloths\Application\Exception\Pass
     */
    public function testPass()
    {
        $application = new Application();
        $application->pass();
    }

    public function testAccessDenied()
    {
        $application = new Application();
        try {
            $application->accessDenied();
        } catch (AccessDenied $e) {

        }

        $this->assertSame(403, $e->getCode());
        $this->assertSame('Access denied', $e->getMessage());

        $application = new Application();
        try {
            $application->accessDenied('message');
        } catch (AccessDenied $e) {

        }

        $this->assertSame('message', $e->getMessage());
    }

    public function testForwardWithRequestObject()
    {
        $application = new Application();
        $request = $this->getMock('Sloths\Http\RequestInterface');

        $this->assertSame($request, $application->forward($request));
    }

    public function testForwardWithPath()
    {
        $application = new Application();
        $application->getRequest()->setMethod('GET')->setParams(new Parameters(['foo' => 'bar', 'bar' => 'baz']));

        $request = $application->forward('/foo', ['bar' => 'qux']);
        $this->assertSame('/foo', $request->getPath());
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame(['foo' => 'bar', 'bar' => 'qux'], $request->getParams()->toArray());

        $request = $application->forward('POST /bar');
        $this->assertSame('/bar', $request->getPath());
        $this->assertSame('POST', $request->getMethod());
    }

    /**
     * @dataProvider dataProviderTestSendBody
     */
    public function testSendBody($responseBody, $expectedOutputString)
    {
        $this->expectOutputString($expectedOutputString);
        $application = new Application();
        $application->getResponse()->setBody($responseBody);
        $application->send();
    }

    public function dataProviderTestSendBody()
    {
        return [
            ['foo', 'foo'],
            [['foo' => 'bar'], json_encode(['foo' => 'bar'])],
            [new JsonData(['foo' => 'bar']), json_encode(['foo' => 'bar'])]
        ];
    }

    public function testSendHeader()
    {
        $this->expectOutputString('["foo"]');

        $application = $this->getMock('Sloths\Application\Application', ['sendHeader']);
        $application->expects($this->at(0))->method('sendHeader')->with('HTTP/1.1 200 OK');
        $application->expects($this->at(1))->method('sendHeader')->with('Content-Type: application/json');

        $application->getResponse()->setBody(['foo']);
        $application->send();
    }

    public function testRunShouldTriggerEventBeforeAndAfterAndNotFound()
    {
        $application = $this->getMock('Sloths\Application\Application', ['triggerEventListener', 'notFound']);
        $application->expects($this->at(0))->method('triggerEventListener')->with('boot');
        $application->expects($this->at(1))->method('triggerEventListener')->with('booted');
        $application->expects($this->at(2))->method('triggerEventListener')->with('before');
        $application->expects($this->at(3))->method('triggerEventListener')->with('after');
        $application->expects($this->once())->method('notFound');
        $application->run();
    }

    public function testTriggerEventBeforeReturnsResponseObject()
    {
        $this->expectOutputString('foo');

        $application = new Application();
        $application->addEventListener('before', function() {
            return 'foo';
        });

        $application->run();
    }

    public function testResolveRequestReturnFalseShouldCallNotFound()
    {
        $application = $this->getMock('Sloths\Application\Application', ['resolveRequest', 'notFound']);
        $application->expects($this->once())->method('resolveRequest')->willReturn(false);
        $application->expects($this->once())->method('notFound');

        $application->run();
    }

    public function testRunWithMathRoute()
    {
        $this->expectOutputString('content');

        $callbackObject = $this->getMock('Callback', ['method']);
        $callbackObject->expects($this->once())->method('method')->with('foo', 'bar')->willReturn('content');

        $route = $this->getMock('Sloths\Routing\Route', ['match', 'getCallback'], [], '', false);
        $route->expects($this->once())->method('match')->with('POST', '/foo')->willReturn(['foo', 'bar']);
        $route->expects($this->once())->method('getCallback')->willReturn([$callbackObject, 'method']);

        $application = new Application();
        $application->getRouter()->add($route);
        $application->getRequest()->setMethod('POST')->setPath('/foo');


        $application->run();
    }

    public function testRouteCallbackReturnsResponseObject()
    {
        $this->expectOutputString('content');

        $response = new Response();
        $response->setBody('content');

        $callbackObject = $this->getMock('Callback', ['method']);
        $callbackObject->expects($this->once())->method('method')->willReturn($response);

        $route = $this->getMock('Sloths\Routing\Route', ['match', 'getCallback'], [], '', false);
        $route->expects($this->once())->method('match')->willReturn([]);
        $route->expects($this->once())->method('getCallback')->willReturn([$callbackObject, 'method']);

        $application = new Application();
        $application->getRouter()->add($route);

        $application->run();
        $this->assertSame($response, $application->getResponse());
    }

    public function testRouteCallbackReturnsRequestObject()
    {
        $request = new Request();

        $callbackObject = $this->getMock('Callback', ['method']);
        $callbackObject->expects($this->once())->method('method')->willReturn($request);

        $route = $this->getMock('Sloths\Routing\Route', ['match', 'getCallback'], [], '', false);
        $route->expects($this->at(0))->method('match')->willReturn([]);
        $route->expects($this->at(1))->method('match')->willReturn(false);
        $route->expects($this->once())->method('getCallback')->willReturn([$callbackObject, 'method']);

        $application = $this->getMock('Sloths\Application\Application', ['notFound']);
        $application->getRouter()->add($route);

        $application->run();
        $this->assertSame($request, $application->getRequest());
    }

    public function testPassShouldPassToTheNextRoute()
    {
        $this->expectOutputString('from route2');
        $application = new Application();

        $route1 = new Route('GET', '/foo', function() use ($application, &$route1Called) {
            $route1Called = true;
            $application->pass();
        });
        $route2 = new Route('GET', '/foo', function() {
            return 'from route2';
        });

        $application->getRouter()->add($route1);
        $application->getRouter()->add($route2);

        $application->getRequest()->setMethod('GET')->setPath('/foo');
        $application->run();

        $this->assertTrue($route1Called);
    }

    public function test__get()
    {
        $serviceManager = $this->getMock('ServiceManager', ['has', 'get']);
        $serviceManager->expects($this->at(0))->method('has')->with('foo')->willReturn(true);
        $serviceManager->expects($this->at(1))->method('get')->with('foo')->willReturn('service');
        $serviceManager->expects($this->at(2))->method('has')->with('bar')->willReturn(false);

        $application = $this->getMock('Sloths\Application\Application', ['getServiceManager', 'getDynamicProperty']);
        $application->expects($this->exactly(2))->method('getServiceManager')->willReturn($serviceManager);
        $application->expects($this->once())->method('getDynamicProperty')->with('bar');

        $application->foo;
        $application->bar;
    }

    public function testDebugMode()
    {
        $application = new Application();

        $this->assertFalse($application->getDebug());

        $application->setDebug(true);
        $this->assertTrue($application->getDebug());
    }

    /**
     * @dataProvider dataProviderTestBaseUrl
     */
    public function testBaseUrl($baseUrl, $expected)
    {
        $application = new Application();
        $application->setBaseUrl($baseUrl);

        $this->assertSame($expected, $application->getBaseUrl(false));
    }

    public function dataProviderTestBaseUrl()
    {
        return [
            ['', '/'],
            ['/', '/'],
            ['/foo', '/foo'],
            ['/foo//', '/foo'],
            ['http://foo', 'http://foo'],
            ['http://foo//', 'http://foo'],
        ];
    }

    /**
     * @dataProvider dataProviderTestLoadRouteFromFile
     */
    public function testLoadRouteFromFile($requestMethod, $requestPath)
    {
        $this->expectOutputString("$requestMethod $requestPath");

        $application = new Application();
        $application->setResourceDirectory(__DIR__ . '/fixtures/application');
        $application->getRequest()->setMethod($requestMethod)->setPath($requestPath);
        $application->run();
    }

    public function dataProviderTestLoadRouteFromFile()
    {
        return [
            ['GET', '/'],
            ['GET', '/posts'],
            ['POST', '/posts'],
            ['GET', '/posts/comments'],
            ['GET', '/foo/bar/baz/qux'],
            ['GET', '/foo/bar/baz/qux/qot'],
        ];
    }

    public function test__callFromDynamicMethod()
    {
        $application = new Application();
        $application->addDynamicMethod('foo', function() {return 'bar';});
        $this->assertSame('bar', $application->foo());
    }
}

class JsonData implements \JsonSerializable
{
    protected $data;
    public function __construct(array $data)
    {
        $this->data = $data;
    }

    public function jsonSerialize()
    {
        return $this->data;
    }
}