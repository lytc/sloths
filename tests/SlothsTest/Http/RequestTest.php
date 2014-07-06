<?php

namespace SlothsTest\Http;

use Sloths\Http\Message\Parameters;
use Sloths\Http\Request;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Request
 */
class RequestTest extends TestCase
{
    public function testServerVars()
    {
        $request = new Request();
        $this->assertInstanceOf('Sloths\Http\Message\Parameters', $request->getServerVars());

        $serverVars = new Parameters();
        $request->setServerVars($serverVars);
        $this->assertSame($serverVars, $request->getServerVars());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidServerVarsShouldThrowAnException()
    {
        $request = new Request();
        $request->setServerVars('foo');
    }

    public function testHeaders()
    {
        $request = new Request();
        $request->setServerVars(['HTTP_FOO' => 'bar']);
        $this->assertSame('bar', $request->getHeaders()->Foo);
    }

    public function testGetReferrer()
    {
        $request = new Request();
        $request->setServerVars(['HTTP_REFERER' => 'bar']);
        $this->assertSame('bar', $request->getReferrer());
    }

    /**
     * @dataProvider dataProviderTestGetPath
     */
    public function testGetPath($expected, $uri)
    {
        $request = new Request();
        $request->setServerVars(['REQUEST_URI' => $uri]);
        $this->assertSame($expected, $request->getPath());
    }

    public function dataProviderTestGetPath()
    {
        return [
            ['/', '/'],
            ['/', '//'],
            ['/', '// '],
            ['/foo', '/foo'],
            ['/foo', '/foo/'],
            ['/foo', '/foo//'],
            ['/foo', '/foo// '],
            ['/foo', '/foo?foo=1&bar=2'],
            ['/foo', '/foo//?foo=1&bar=2'],
        ];
    }

    public function testGetOriginalMethod()
    {
        $request = new Request();
        $request->setServerVars(['REQUEST_METHOD' => 'POST']);
        $this->assertSame('POST', $request->getOriginalMethod());
    }

    /**
     * @dataProvider dataProviderTestGetMethod
     */
    public function testGetMethod($expected, $request)
    {
        $this->assertSame($expected, $request->getMethod());
    }

    public function dataProviderTestGetMethod()
    {
        return [
            ['POST', (new Request())->setServerVars(['REQUEST_METHOD' => 'POST'])],
            ['PUT', (new Request())->setServerVars(['REQUEST_METHOD' => 'POST'])->setPostParams(['_method' => 'PUT'])],
            ['PUT', (new Request())->setServerVars(['REQUEST_METHOD' => 'POST', 'HTTP_X_HTTP_METHOD_OVERRIDE' => 'PUT'])],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetHost
     */
    public function testGetHost($expected, $request, $withPort = false)
    {
        $this->assertSame($expected, $request->getHost($withPort));
    }

    public function dataProviderTestGetHost()
    {
        return [
            ['example.com', (new Request())->setServerVars(['HTTP_HOST' => 'example.com'])],
            ['example.com', (new Request())->setServerVars(['HTTP_HOST' => 'example.com:80'])],
            ['example.com', (new Request())->setServerVars(['SERVER_NAME' => 'example.com'])],
            ['example.com:80', (new Request())->setServerVars(['HTTP_HOST' => 'example.com:80', 'SERVER_PORT' => '80']), true],
        ];
    }

    public function testGetClientIp()
    {
        $request = new Request();
        $request->setServerVars(['REMOTE_ADDR' => '127.0.0.1']);
        $this->assertSame('127.0.0.1', $request->getClientIp());
    }

    public function testGetUserAgent()
    {
        $request = new Request();
        $request->setServerVars(['USER_AGENT' => 'foo']);
        $this->assertSame('foo', $request->getUserAgent());
    }

    public function testAccepts()
    {
        $request = new Request();
        $request->setServerVars(['HTTP_ACCEPT' => 'foo,bar']);
        $this->assertSame(['foo', 'bar'], $request->getAccepts());
        $this->assertTrue($request->isAccept('foo'));
    }

    public function testGetContentType()
    {
        $request = new Request();
        $request->setServerVars(['HTTP_CONTENT_TYPE' => 'text/xml']);
        $this->assertSame('text/xml', $request->getContentType());
    }

    public function testIsXhr()
    {
        $request = new Request();
        $request->setServerVars(['HTTP_X_REQUESTED_WITH' => 'XMLHttpRequest']);
        $this->assertTrue($request->isXhr());
    }

    /**
     * @dataProvider dataProviderTestIsMethod
     */
    public function testIsMethod($requestMethod, $method)
    {
        $request = new Request();
        $request->setServerVars(['REQUEST_METHOD' => $requestMethod]);
        $this->assertTrue($request->{$method}());
    }

    public function dataProviderTestIsMethod()
    {
        return [
            ['HEAD', 'isHead'],
            ['GET', 'isGet'],
            ['POST', 'isPost'],
            ['PUT', 'isPut'],
            ['PATCH', 'isPatch'],
            ['DELETE', 'isDelete'],
            ['OPTIONS', 'isOptions'],
            ['TRACE', 'isTrace'],
            ['CONNECT', 'isConnect'],
        ];
    }

    /**
     * @dataProvider dataProviderTestGetUrl
     */
    public function testGetUrl($expected, $request)
    {
        $this->assertSame($expected, $request->getUrl());
    }

    public function dataProviderTestGetUrl()
    {
        return [
            ['http://example.com', (new Request())->setServerVars(['HTTP_HOST' => 'example.com', 'SERVER_PORT' => '80'])],
            ['https://example.com', (new Request())->setServerVars(['HTTP_HOST' => 'example.com', 'SERVER_PORT' => '443', 'HTTPS' => 'on'])],
            ['http://example.com:8080', (new Request())->setServerVars(['HTTP_HOST' => 'example.com', 'SERVER_PORT' => '8080'])],
            ['http://example.com/foo', (new Request())->setServerVars(['HTTP_HOST' => 'example.com', 'SERVER_PORT' => '80', 'REQUEST_URI' => '/foo'])],
            ['http://example.com:8080/foo', (new Request())->setServerVars(['HTTP_HOST' => 'example.com:8080', 'SERVER_PORT' => '8080', 'REQUEST_URI' => '/foo'])],
        ];
    }

    public function testQueryParams()
    {
        $_GET = ['foo' => 'bar'];
        $request = new Request();
        $this->assertSame('bar', $request->getQueryParams()->foo);
    }

    public function testParams()
    {
        $_POST = ['foo' => 'bar'];
        $request = new Request();
        $this->assertSame('bar', $request->getParams()->foo);
    }

    public function testCookieParams()
    {
        $_COOKIE = ['foo' => 'bar'];
        $request = new Request();
        $this->assertSame('bar', $request->getCookieParams()->foo);
    }

    /**
     * @dataProvider dataProviderTestFileParams
     */
    public function testFileParams($files, $expectedFiles)
    {
        $_FILES = $files;
        $request = new Request();
        $this->assertSame($expectedFiles, $request->getFileParams()->toArray());
    }

    public function dataProviderTestFileParams()
    {
        return [
            // single file: name="foo", name="bar"
            [
                [
                    'foo' => ['name' => 'foo.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/fooxxx', 'error' => 0, 'size' => 1],
                    'bar' => ['name' => 'bar.jpg', 'type' => 'image/jpg', 'tmp_name' => '/tmp/barxxx', 'error' => 0, 'size' => 2],
                ],
                [
                    'foo' => ['name' => 'foo.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/fooxxx', 'error' => 0, 'size' => 1],
                    'bar' => ['name' => 'bar.jpg', 'type' => 'image/jpg', 'tmp_name' => '/tmp/barxxx', 'error' => 0, 'size' => 2],
                ]
            ],
            // name with brackets and int keys
            [
                [
                    'foo' => [
                        'name' => ['foo.txt', 'bar.jpg'],
                        'type' => ['text/plain', 'image/jpg'],
                        'tmp_name' => ['/tmp/fooxxx', '/tmp/barxxx'],
                        'error' => [0, 0],
                        'size' => [1, 2]
                    ]
                ],
                [
                    'foo' => [
                        ['name' => 'foo.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/fooxxx', 'error' => 0, 'size' => 1],
                        ['name' => 'bar.jpg', 'type' => 'image/jpg', 'tmp_name' => '/tmp/barxxx', 'error' => 0, 'size' => 2],
                    ]
                ]
            ],
            // name with brackets and string keys: foo[one], foo[two]
            [
                [
                    'foo' => [
                        'name' => ['one' => 'foo.txt', 'two' => 'bar.jpg'],
                        'type' => ['one' => 'text/plain', 'two' => 'image/jpg'],
                        'tmp_name' => ['one' => '/tmp/fooxxx', 'two' => '/tmp/barxxx'],
                        'error' => ['one' => 0, 'two' => 0],
                        'size' => ['one' => 1, 'two' => 2]
                    ]
                ],
                [
                    'foo' => [
                        'one' => ['name' => 'foo.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/fooxxx', 'error' => 0, 'size' => 1],
                        'two' => ['name' => 'bar.jpg', 'type' => 'image/jpg', 'tmp_name' => '/tmp/barxxx', 'error' => 0, 'size' => 2],
                    ]
                ]
            ],

            // name with multiple level brackets foo[], foo[][], foo[][][]
            [
                [
                    'foo' => [
                        'name' => [
                            'foo.txt',
                            ['bar.jpg'],
                            [['baz.css']]
                        ],
                        'type' => [
                            'text/plain',
                            ['image/jpg'],
                            [['text/css']]
                        ],
                        'tmp_name' => [
                            '/tmp/fooxxx',
                            ['/tmp/barxxx'],
                            [['/tmp/bazxxx']]
                        ],
                        'error' => [
                            0,
                            [0],
                            [[0]]
                        ],
                        'size' => [
                            1,
                            [2],
                            [[3]]
                        ]
                    ]
                ],
                [
                    'foo' => [
                        ['name' => 'foo.txt', 'type' => 'text/plain', 'tmp_name' => '/tmp/fooxxx', 'error' => 0, 'size' => 1],
                        [['name' => 'bar.jpg', 'type' => 'image/jpg', 'tmp_name' => '/tmp/barxxx', 'error' => 0, 'size' => 2]],
                        [[['name' => 'baz.css', 'type' => 'text/css', 'tmp_name' => '/tmp/bazxxx', 'error' => 0, 'size' => 3]]]
                    ]
                ]
            ]
        ];
    }
}