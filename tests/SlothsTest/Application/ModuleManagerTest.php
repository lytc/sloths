<?php

namespace SlothsTest\Application;

use Sloths\Application\Application;
use Sloths\Http\Request;
use SlothsTest\TestCase;
use Sloths\Application\ModuleManager;

/**
 * @covers Sloths\Application\ModuleManager
 */
class ModuleManagerTest extends TestCase
{
    /**
     * @expectedException \InvalidArgumentException
     */
    public function testAddInvalidApplicationShouldThrowAnException()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->add('foo', [], new \stdClass());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUndefinedApplicationShouldThrowAnException()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->get('foo');
    }

    public function testGet()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->setDirectory(__DIR__  . '/fixtures/module-manager');
        $moduleManager->add('foo');

        $app = $moduleManager->get('foo');
        $this->assertInstanceOf('Sloths\Application\ApplicationInterface', $app);

        $this->assertSame($app, $moduleManager->get('foo'));
    }

    /**
     * @expectedException \UnexpectedValueException
     */
    public function testGetWithInvalidApplicationInstanceShouldThrowAnException()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->add('foo', [], __NAMESPACE__ . '\NonApplication');
        $moduleManager->get('foo');
    }

    public function testGetApplicationByClosure()
    {
        $application = new Application();

        $moduleManager = new ModuleManager();
        $moduleManager->setDirectory(__DIR__  . '/fixtures/module-manager');

        $moduleManager->add('foo', [], function() use ($application) {
            return $application;
        });

        $this->assertSame($application, $moduleManager->get('foo'));
    }

    public function testGetApplicationShouldTriggerEventCreate()
    {
        $moduleManager = $this->getMock('Sloths\Application\ModuleManager', ['triggerEventListener']);
        $moduleManager->expects($this->once())->method('triggerEventListener')->with('create');

        $moduleManager->setDirectory(__DIR__  . '/fixtures/module-manager');
        $moduleManager->add('foo');
        $moduleManager->get('foo');
    }

    public function testApplicationWithBaseUrl()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->setDirectory(__DIR__  . '/fixtures/module-manager');
        $moduleManager->getRequest()->setPath('/foo/bar/baz');

        $moduleManager->add('foo', ['baseUrl' => '/foo/bar/']);

        $app = $moduleManager->get('foo');
        $this->assertInstanceOf('Sloths\Application\ApplicationInterface', $app);
        $this->assertSame('/foo/bar', $app->getBaseUrl());
    }

    public function testValidateShouldReturnsTrueIfAllConditionIsPassed()
    {
        $callback = function($request) use (&$expectedRequest) {
            $expectedRequest = $request;
            return true;
        };

        $moduleManager = $this->getMock('Sloths\Application\ModuleManager', ['validateFoo', 'validateBar']);
        $moduleManager->expects($this->once())->method('validateFoo')->with('foo')->willReturn(true);
        $moduleManager->expects($this->once())->method('validateBar')->with('bar')->willReturn(true);

        $result = $moduleManager->validate(['foo' => 'foo', 'bar' => 'bar', 'baz' => $callback]);

        $this->assertTrue($result);
        $this->assertSame($moduleManager->getRequest(), $expectedRequest);
    }

    public function testValidateShouldReturnsTrueIfOneOFConditionIsFailed()
    {
        $callback = function() {
            return false;
        };

        $moduleManager = $this->getMock('Sloths\Application\ModuleManager', ['validateFoo', 'validateBar']);
        $moduleManager->expects($this->once())->method('validateFoo')->with('foo')->willReturn(true);
        $moduleManager->expects($this->once())->method('validateBar')->with('bar')->willReturn(false);

        $result = $moduleManager->validate(['foo' => 'foo', 'bar' => 'bar']);
        $this->assertFalse($result);

        $result = $moduleManager->validate([$callback]);
        $this->assertFalse($result);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testValidateWithUndefinedValidatorShouldThrowAnException()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->validate(['foo' => 'foo']);
    }

    /**
     * @dataProvider dataProviderTestValidateBaseUrl
     */
    public function testValidateBaseUrl($baseUrl, $request, $expected)
    {
        $moduleManager = new ModuleManager();
        $moduleManager->setRequest($request);

        $result = $moduleManager->validate(['baseUrl' => $baseUrl]);
        $this->assertSame($expected, $result);
    }

    public function dataProviderTestValidateBaseUrl()
    {
        $request = $this->getMock('Sloths\Http\RequestInterface');
        $request->expects($this->any())->method('getScheme')->willReturn('http');
        $request->expects($this->any())->method('getHost')->willReturn('example.com');
        $request->expects($this->any())->method('getPort')->willReturn(8080);
        $request->expects($this->any())->method('getPath')->willReturn('/foo/bar/baz');

        return [
            ['/', (new Request())->setPath('/'), true],
            ['/', (new Request())->setPath('/foo/bar'), true],
            ['/foo/', (new Request())->setPath('/foo/bar'), true],
            ['/bar/', (new Request())->setPath('/foo/bar'), false],
            ['http://example.com:8080/foo/', $request, true],
            ['http://example.com:8080', $request, true],
            ['http://example.com', $request, true],
            ['//example.com', $request, true],
            ['//example.com/foo/', $request, true],
            ['https://example.com:8080/foo/', $request, false],
            ['http://foo.com:8080/foo/', $request, false],
            ['http://example.com:8443/foo/', $request, false],
            ['http://example.com:8080/bar/', $request, false],
        ];
    }

    /**
     * @dataProvider dataProviderTestValidateBaseUrlRegex
     */
    public function testValidateBaseUrlRegex($baseUrlRegex, $request, $expected)
    {
        $moduleManager = new ModuleManager();
        $moduleManager->setRequest($request);

        $result = $moduleManager->validate(['baseUrlRegex' => $baseUrlRegex]);
        $this->assertSame($expected, $result);
    }

    public function dataProviderTestValidateBaseUrlRegex()
    {
        $request = $this->getMock('Sloths\Http\RequestInterface');
        $request->expects($this->any())->method('getUrl')->willReturn('http://example.com:8080/foo/bar/baz');

        return [
            ['/^http:\/\/example\.com:8080\/foo\//', $request, true],
            ['/^http:\/\/example\.com:8080\//', $request, true],
            ['/^http:\/\/example\.com\:(8080|8081)\//', $request, true],
            ['/^http:\/\/example\.com:8082\//', $request, false],
        ];
    }

    public function testValidateHost()
    {
        $request = $this->getMock('Sloths\Http\RequestInterface');
        $request->expects($this->any())->method('getHost')->willReturn('example.com');

        $moduleManager = new ModuleManager();
        $moduleManager->setRequest($request);

        $this->assertTrue($moduleManager->validate(['host' => 'example.com']));
        $this->assertFalse($moduleManager->validate(['host' => 'foo.com']));
    }

    public function testValidateHostRegex()
    {
        $request = $this->getMock('Sloths\Http\RequestInterface');
        $request->expects($this->any())->method('getHost')->willReturn('foo.example.com:8080');

        $moduleManager = new ModuleManager();
        $moduleManager->setRequest($request);

        $this->assertTrue($moduleManager->validate(['hostRegex' => '/^foo\.example\.com/']));
        $this->assertTrue($moduleManager->validate(['hostRegex' => '/^(\w+)\.example\.com/']));
        $this->assertTrue($moduleManager->validate(['hostRegex' => '/^(\w+)\.example\.com:(8080|8081)/']));
        $this->assertFalse($moduleManager->validate(['host' => '/^bar\.example\.com/']));
        $this->assertFalse($moduleManager->validate(['hostRegex' => '/^(\w+)\.example\.com:(8081|8082)/']));
    }

    public function testResolve()
    {
        $adminApplication = new Application();
        $authApplication = new Application();
        $contentApplication = new Application();

        $moduleManager = new ModuleManager();
        $moduleManager->setDirectory(__DIR__  . '/fixtures/module-manager');

        $moduleManager->add('admin', ['baseUrl' => '/admin/'], function() use ($adminApplication) {
            return $adminApplication;
        });
        $moduleManager->add('auth', ['baseUrl' => '/auth/'], function() use ($authApplication) {
            return $authApplication;
        });
        $moduleManager->add('content', [], function() use ($contentApplication) {
            return $contentApplication;
        });

        $request = $moduleManager->getRequest();
        $request->setPath('/admin');

        $this->assertSame($adminApplication, $moduleManager->resolve());

        $request->setPath('/admin/foo/bar');
        $this->assertSame($adminApplication, $moduleManager->resolve());

        $request->setPath('/auth');
        $this->assertSame($authApplication, $moduleManager->resolve());

        $request->setPath('/foo/bar/baz');
        $this->assertSame($contentApplication, $moduleManager->resolve());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testResolveWithoutDefaultApplicationShouldThrowAnException()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->resolve();
    }

    public function testResolveWithCallback()
    {
        $moduleManager = new ModuleManager();
        $moduleManager->setDirectory(__DIR__  . '/fixtures/module-manager');

        $contentApplication = new Application();

        $moduleManager->add('content', [], function() use ($contentApplication) {
            return $contentApplication;
        });

        $callback = function($application) use (&$expectedApplication) {
            $expectedApplication = $application;
        };

        $this->assertSame($contentApplication, $moduleManager->resolve($callback));
        $this->assertSame($contentApplication, $expectedApplication);
    }
}

class NonApplication
{

}