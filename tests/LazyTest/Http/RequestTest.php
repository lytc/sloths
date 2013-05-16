<?php

namespace LazyTest\Http;
use Lazy\Http\Request;
use Lazy\Environment\Environment;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testEnvInstanceOfEnvironment()
    {
        $request = new Request();
        $this->assertInstanceOf('Lazy\Environment\Environment', $request->env());
    }

    public function getSetEnv()
    {
        $request = new Request();
        $env = new Environment();
        $request->env($env);
        $this->assertSame($env, $request->env());
    }

    public function testGetAllHeaders()
    {
        $_SERVER['HTTP_FOO'] = 'foo';
        $_SERVER['HTTP_BAR'] = 'bar';
        $request  = new Request();
        $this->assertSame(['FOO' => 'foo', 'BAR' => 'bar'], $request->headers());
        $this->assertSame(['FOO' => 'foo', 'BAR' => 'bar'], $request->header());
    }

    public function testGetHeader()
    {
        $_SERVER['HTTP_FOO'] = 'foo';
        $_SERVER['HTTP_BAR'] = 'bar';
        $request  = new Request();
        $this->assertSame('foo', $request->header('FOO'));
    }

    public function testSetHeader()
    {
        $request  = new Request();
        $request->header('BAZ', 'baz');
        $this->assertSame('baz', $request->header('BAZ'));
    }

    public function testPathInfo()
    {
        $request = new Request();
        $this->assertSame('/', $request->pathInfo());

        $_SERVER['PATH_INFO'] = '/';
        $request = new Request();
        $this->assertSame($_SERVER['PATH_INFO'], $request->pathInfo());
    }

    public function testPathInfoOverrides()
    {
        $_SERVER['PATH_INFO'] = '/foo';

        $request = new Request();
        $request->pathInfoOverrides(['/foo' => '/bar']);
        $this->assertSame('/bar', $request->pathInfo());
    }

    public function testSetPathInfo()
    {
        $request = new Request();
        $request->pathInfo('/foo/bar');
        $this->assertSame('/foo/bar', $request->pathInfo());
    }

    public function testGetReferrer()
    {
        $_SERVER['HTTP_REFERER'] = '/foo/bar';
        $request = new Request();
        $this->assertSame('/foo/bar', $request->referrer());
    }

    public function testSetReferrer()
    {
        $request = new Request();
        $request->referrer('/bar/baz');
        $this->assertSame('/bar/baz', $request->referrer());
    }

    public function testGetAndSetServerName()
    {
        $_SERVER['SERVER_NAME'] = 'foo.com';
        $request = new Request();
        $this->assertSame('foo.com', $request->serverName());

        $request->serverName('bar.com');
        $this->assertSame('bar.com', $request->serverName());
    }

    public function testGetAndSetServerPort()
    {
        $_SERVER['SERVER_PORT'] = 80;
        $request = new Request();
        $this->assertSame(80, $request->serverPort());

        $request->serverPort(443);
        $this->assertSame(443, $request->serverPort());
    }

    public function testGetAndSetHost()
    {
        $_SERVER['HTTP_HOST'] = 'foo.com:80';
        $request = new Request();
        $this->assertSame('foo.com:80', $request->host());

        $request->host('bar.com:443');
        $this->assertSame('bar.com:443', $request->host());
    }

    public function testGetAndSetServerIp()
    {
        $_SERVER['SERVER_ADDR'] = '127.0.0.1';
        $request = new Request();
        $this->assertSame('127.0.0.1', $request->serverIp());

        $request->serverIp('127.0.0.2');
        $this->assertSame('127.0.0.2', $request->serverIp());
    }

    public function testGetAndSetClientIp()
    {
        $_SERVER['REMOTE_ADDR'] = '127.0.0.1';
        $request = new Request();
        $this->assertSame('127.0.0.1', $request->clientIp());

        $request->clientIp('127.0.0.2');
        $this->assertSame('127.0.0.2', $request->clientIp());
    }

    public function testGetAndSetClientPort()
    {
        $_SERVER['REMOTE_PORT'] = 1234;
        $request = new Request();
        $this->assertSame(1234, $request->clientPort());

        $request->clientPort(2345);
        $this->assertSame(2345, $request->clientPort());
    }

    public function testGetAndSetUserAgent()
    {
        $_SERVER['HTTP_USER_AGENT'] = 'foo';
        $request = new Request();
        $this->assertSame('foo', $request->userAgent());

        $request->userAgent('bar');
        $this->assertSame('bar', $request->userAgent());
    }

    public function testGetAndSetAccepts()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml';
        $request = new Request();
        $this->assertSame(['text/html', 'application/xhtml+xml'], $request->accepts());

        $request = new Request();
        $request->accepts('text/html,application/xml');
        $this->assertSame(['text/html', 'application/xml'], $request->accepts());
    }

    public function testIsAccept()
    {
        $_SERVER['HTTP_ACCEPT'] = 'text/html,application/xhtml+xml';
        $request = new Request();
        $this->assertTrue($request->isAccept('text/html'));
        $this->assertFalse($request->isAccept('application/xml'));
    }

    public function testGetAndSetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'GET';
        $request = new Request();
        $this->assertSame('GET', $request->method());

        $request = new Request();
        $request->method('POST');
        $this->assertSame('POST', $request->method());
    }

    public function testMethodOverride()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_POST['_method'] = 'PUT';
        $request = new Request();
        $this->assertSame('PUT', $request->method());
    }

    public function testIsMethod()
    {
        $_POST = [];
        $methods = ['GET', 'POST', 'PUT', 'DELETE', 'OPTIONS', 'TRACE', 'PATCH', 'CONNECT', 'PROPFIND'];
        foreach ($methods as $methodDetect) {
            $request = new Request();
            $request->method($methodDetect);

            foreach ($methods as $method) {
                $methodName = ucfirst(strtolower($method));
                if ($method == $methodDetect) {
                    $this->assertTrue($request->{"is{$methodName}"}());
                } else {
                    $this->assertFalse($request->{"is{$methodName}"}());
                }
            }
        }
    }

    public function testIsXhr()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $request = new Request();
        $this->assertTrue($request->isXhr());
    }

    public function testIsSecure()
    {
        $_SERVER['HTTPS'] = 'on';
        $request = new Request();
        $this->assertTrue($request->isSecure());
    }

    public function testGetSchema()
    {
        $_SERVER = [];
        $request = new Request();
        $this->assertSame('http', $request->scheme());

        $_SERVER['HTTPS'] = 'on';
        $request = new Request();
        $this->assertSame('https', $request->scheme());
    }

    public function testGetAndSetBody()
    {
        $request = new Request();
        $this->assertSame(file_get_contents('php://input'), $request->body());

        $request = new Request();
        $request->body('foo');
        $this->assertSame('foo', $request->body());
    }

    /**
     * @expectedException \Lazy\Http\Exception\Exception
     * @expectedExceptionMessage Call undefined method foo
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $request = new Request();
        $request->foo();
    }
}