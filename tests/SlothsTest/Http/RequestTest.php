<?php

namespace SlothsTest\Http;

use Sloths\Http\Request;

class RequestTest extends \PHPUnit_Framework_TestCase
{
    public function testAutoMapSuperGlobals()
    {
        $_GET = ['foo' => 'bar'];
        $request = new Request();
        $this->assertSame('bar', $request->getGetVar('foo'));
    }

    public function testGetPath()
    {
        $request = new Request([
            '_SERVER' => ['PATH_INFO' => '/foo/bar']
        ]);
        $this->assertSame('/foo/bar', $request->getPath());

        $request = new Request([
            '_SERVER' => ['PATH_INFO' => '/foo/bar//']
        ]);
        $this->assertSame('/foo/bar', $request->getPath());


        $request = new Request([
            '_SERVER' => ['REQUEST_URI' => '/foo/bar/baz']
        ]);
        $this->assertSame('/foo/bar/baz', $request->getPath());

        $request = new Request([
            '_SERVER' => ['REQUEST_URI' => '/foo/bar/baz/?foo=bar&bar=baz']
        ]);
        $this->assertSame('/foo/bar/baz', $request->getPath());
    }

    public function testTrimmedPath()
    {
        $request = new Request([
            '_SERVER' => ['PATH_INFO' => '/']
        ]);
        $this->assertSame('/', $request->getPath());

        $request = new Request([
            '_SERVER' => ['PATH_INFO' => '///']
        ]);
        $this->assertSame('/', $request->getPath());

        $request = new Request([
            '_SERVER' => ['PATH_INFO' => '/foo/']
        ]);
        $this->assertSame('/foo', $request->getPath());

        $request = new Request([
            '_SERVER' => ['PATH_INFO' => '/foo/bar//']
        ]);
        $this->assertSame('/foo/bar', $request->getPath());
    }

    public function testGetOriginalMethod()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET']
        ]);
        $this->assertSame('GET', $request->getOriginalMethod());
    }

    public function testGetMethod()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET']
        ]);
        $this->assertSame('GET', $request->getMethod());
        $this->assertSame($request->getOriginalMethod(), $request->getMethod());
    }

    public function testGetCustomMethod()
    {
        $request = new Request([
            '_SERVER'   => ['REQUEST_METHOD' => 'POST'],
            '_POST'     => ['_method' => 'PUT']
        ]);
        $this->assertSame('PUT', $request->getMethod());

        $request = new Request([
            '_SERVER'   => ['REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'PUT'],
        ]);

        $this->assertSame('PUT', $request->getMethod());
    }

    public function testGetServerVars()
    {
        $request = new Request([
            '_SERVER' => [
                'HTTP_REFERER' => '/foo',
                'SERVER_NAME' => 'foo',
                'SERVER_PORT' => '1111',
                'HTTP_HOST' => 'foo',
                'SERVER_ADDR' => '2222',
                'REMOTE_ADDR' => 'foo',
                'REMOTE_PORT' => '1111',
                'HTTP_USER_AGENT' => 'firefox',
                'HTTP_ACCEPT' => 'foo,bar',
                'HTTPS' => 'on',
                'HTTP_CONTENT_TYPE' => 'text/html'
            ]
        ]);

        $this->assertSame('/foo', $request->getReferrer());
        $this->assertSame('foo', $request->getServerName());
        $this->assertSame('1111', $request->getServerPort());
        $this->assertSame('foo', $request->getHost());
        $this->assertSame('2222', $request->getServerIp());
        $this->assertSame('foo', $request->getClientIp());
        $this->assertSame('1111', $request->getClientPort());
        $this->assertSame('firefox', $request->getUserAgent());
        $this->assertSame(['foo', 'bar'], $request->getAccepts());
        $this->assertTrue($request->isAccept('foo'));
        $this->assertFalse($request->isAccept('baz'));
        $this->assertTrue($request->isSecure());
        $this->assertSame('https', $request->getScheme());
        $this->assertSame('text/html', $request->getContentType());
    }

    public function testGetHeader()
    {
        $request = new Request([
            '_SERVER' => [
                'HTTP_FOO' => 'foo',
                'HTTP_BAR' => 'bar',
            ]
        ]);

        $this->assertSame(['Foo' => 'foo', 'Bar' => 'bar'], $request->getHeaders());
        $this->assertTrue($request->hasHeader('foo'));

        $expected = "Foo: foo\r\nBar: bar";
        $this->assertSame($expected, $request->getHeaderAsString());
    }

    public function testHasVar()
    {
        $request = new Request([
            '_GET' => ['foo' => 'foo'],
            '_POST' => ['bar' => 'bar'],
            '_COOKIE' => ['baz' => 'baz'],
            '_FILES' => ['qux' => 'qux']
        ]);

        $this->assertTrue($request->hasGetVar('foo'));
        $this->assertFalse($request->hasGetVar('bar'));
        $this->assertTrue($request->hasPostVar('bar'));
        $this->assertFalse($request->hasPostVar('foo'));
        $this->assertTrue($request->hasCookieVar('baz'));
        $this->assertFalse($request->hasCookieVar('foo'));
        $this->assertTrue($request->hasFileVar('qux'));
        $this->assertFalse($request->hasFileVar('foo'));
        $this->assertTrue($request->hasVar('foo'));
        $this->assertTrue($request->hasVar('bar'));
        $this->assertTrue($request->hasVar('baz'));
        $this->assertTrue($request->hasVar('qux'));
        $this->assertFalse($request->hasVar('xxxx'));
    }

    public function testGetVar()
    {
        $request = new Request([
            '_REQUEST' => ['foo' => 'bar']
        ]);

        $this->assertSame('bar', $request->getVar('foo'));
    }

    public function testGetVars()
    {
        $request = new Request([
            '_GET' => ['foo' => 'bar', 'bar' => 'baz'],
            '_POST' => ['baz' => 'qux']
        ]);

        $this->assertSame(['foo' => 'bar', 'bar' => 'baz', 'baz' => 'qux'], $request->getVars());
    }

    public function testGetGetVar()
    {
        $request = new Request([
            '_GET' => ['foo' => 'bar']
        ]);

        $this->assertSame('bar', $request->getGetVar('foo'));
    }

    public function testGetGetVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz'];
        $request = new Request([
            '_GET' => $vars
        ]);

        $this->assertSame($vars, $request->getGetVars());
    }

    public function testGetPostVar()
    {
        $request = new Request([
            '_POST' => ['foo' => 'bar']
        ]);

        $this->assertSame('bar', $request->getPostVar('foo'));
    }

    public function testGetPostVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz'];
        $request = new Request([
            '_POST' => $vars
        ]);

        $this->assertSame($vars, $request->getPostVars());
    }


    public function testGetCookieVar()
    {
        $request = new Request([
            '_COOKIE' => ['foo' => 'bar']
        ]);

        $this->assertSame('bar', $request->getCookieVar('foo'));
    }

    public function testGetCookieVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz'];
        $request = new Request([
            '_COOKIE' => $vars
        ]);

        $this->assertSame($vars, $request->getCookieVars());
    }

    public function testGetFileVar()
    {
        $request = new Request([
            '_FILES' => ['foo' => 'bar']
        ]);

        $this->assertSame('bar', $request->getFileVar('foo'));
    }

    public function testGetFileVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz'];
        $request = new Request([
            '_FILES' => $vars
        ]);

        $this->assertSame($vars, $request->getFileVars());
    }

    public function testPickGetVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'buz'];
        $request = new Request([
            '_GET' => $vars
        ]);

        $this->assertSame(['foo' => 'bar', 'baz' => 'buz'], $request->pickGetVars('foo baz'));
    }

    public function testPickPostVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'buz'];
        $request = new Request([
            '_POST' => $vars
        ]);

        $this->assertSame(['foo' => 'bar', 'baz' => 'buz'], $request->pickPostVars('foo baz'));
    }

    public function testPickCookieVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'buz'];
        $request = new Request([
            '_COOKIE' => $vars
        ]);

        $this->assertSame(['foo' => 'bar', 'baz' => 'buz'], $request->pickCookieVars('foo baz'));
    }

    public function testPickFileVars()
    {
        $vars = ['foo' => 'bar', 'bar' => 'baz', 'baz' => 'buz'];
        $request = new Request([
            '_FILES' => $vars
        ]);

        $this->assertSame(['foo' => 'bar', 'baz' => 'buz'], $request->pickFileVars('foo baz'));
    }

    public function testPickVars()
    {
        $getVars = ['foo' => 'bar', 'bar' => 'baz'];
        $postVars = ['bar' => 'baz', 'baz' => 'buz'];
        $cookieVars = ['qux' => 'bar', 'baz' => 'baz'];
        $fileVars = ['wot' => 'bar', 'baz' => 'buz'];

        $request = new Request([
            '_GET' => $getVars,
            '_POST' => $postVars,
            '_COOKIE' => $cookieVars,
            '_FILES' => $fileVars,
        ]);

        $expected = ['foo' => 'bar', 'baz' => 'buz', 'wot' => 'bar'];
        $this->assertSame($expected, $request->pickVars('foo baz wot'));
    }

    public function testIsHead()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'HEAD']
        ]);

        return $this->assertTrue($request->isHead());
    }

    public function testIsGet()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'GET']
        ]);

        return $this->assertTrue($request->isGet());
    }

    public function testIsPost()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'POST']
        ]);

        return $this->assertTrue($request->isPost());
    }

    public function testIsPut()
    {
        $request = new Request([
            '_SERVER'   => ['REQUEST_METHOD' => 'POST'],
            '_GET'      => ['_method' => 'PUT']
        ]);

        return $this->assertTrue($request->isPut());
    }

    public function testIsPatch()
    {
        $request = new Request([
            '_SERVER'   => ['REQUEST_METHOD' => 'POST'],
            '_GET'      => ['_method' => 'PATCH']
        ]);

        return $this->assertTrue($request->isPatch());
    }

    public function testIsDelete()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'POST'],
            '_GET'      => ['_method' => 'DELETE']
        ]);

        return $this->assertTrue($request->isDelete());
    }

    public function testIsOptions()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'OPTIONS']
        ]);

        return $this->assertTrue($request->isOptions());
    }

    public function testIsXhr()
    {
        $request = new Request([
            '_SERVER' => ['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']
        ]);

        $this->assertTrue($request->isXhr());
    }

    public function testDefaultGetMethodCallback()
    {
        Request::setDefaultGetMethodCallback(function() {
            return 'foo';
        });

        $this->assertSame('foo', (new Request())->getMethod());
    }

    public function testCustomGetMethodCallback()
    {
        $request = new Request([
            '_SERVER' => ['REQUEST_METHOD' => 'POST'],
            '_POST' => ['__method__' => 'PUT']
        ]);

        $request->setGetMethodCallback(function($request) {
            $method = $originalMethod = $request->getOriginalMethod();
            if ('POST' == $originalMethod) {
                $method = $request->getVar('__method__')?: $originalMethod;
            }
            return $method;
        });

        $this->assertSame('PUT', $request->getMethod());
    }

    public function testMethodParams()
    {
        $request = $this->getMock('Sloths\Http\Request', ['getVars']);
        $request->expects($this->once())->method('getVars')->willReturn(['foo' => 'foo']);

        $params = $request->params();

        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $params);
        $this->assertSame($params, $request->params());
        $this->assertSame(['foo' => 'foo'], $params->toArray());
    }

    public function testMethodParamsGet()
    {
        $request = $this->getMock('Sloths\Http\Request', ['getGetVars']);
        $request->expects($this->once())->method('getGetVars')->willReturn(['foo' => 'foo']);

        $params = $request->paramsGet();

        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $params);
        $this->assertSame($params, $request->paramsGet());
        $this->assertSame(['foo' => 'foo'], $params->toArray());
    }

    public function testMethodParamsPost()
    {
        $request = $this->getMock('Sloths\Http\Request', ['getPostVars']);
        $request->expects($this->once())->method('getPostVars')->willReturn(['foo' => 'foo']);

        $params = $request->paramsPost();

        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $params);
        $this->assertSame($params, $request->paramsPost());
        $this->assertSame(['foo' => 'foo'], $params->toArray());
    }

    public function testMethodParamsCookie()
    {
        $request = $this->getMock('Sloths\Http\Request', ['getCookieVars']);
        $request->expects($this->once())->method('getCookieVars')->willReturn(['foo' => 'foo']);

        $params = $request->paramsCookie();

        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $params);
        $this->assertSame($params, $request->paramsCookie());
        $this->assertSame(['foo' => 'foo'], $params->toArray());
    }

    public function testMethodHeaders()
    {
        $request = $this->getMock('Sloths\Http\Request', ['getHeaders']);
        $request->expects($this->once())->method('getHeaders')->willReturn(['foo' => 'foo']);

        $headers = $request->headers();

        $this->assertInstanceOf('Sloths\Misc\ArrayContainer', $headers);
        $this->assertSame($headers, $request->headers());
        $this->assertSame(['foo' => 'foo'], $headers->toArray());
    }
}