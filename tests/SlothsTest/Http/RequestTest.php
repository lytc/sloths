<?php

namespace SlothsTest\Http;

use SlothsTest\TestCase;
use Sloths\Http\Request;

/**
 * @covers Sloths\Http\Request
 */
class RequestTest extends TestCase
{
    public function testServerVars()
    {
        $_SERVER['foo'] = 'bar';
        $request = new Request();
        $this->assertSame('bar', $request->getServerVars()->get('foo'));
    }

    public function testGetHeaders()
    {
        $_SERVER['HTTP_FOO'] = 'bar';
        $request = new Request();
        $this->assertSame('bar', $request->getHeaders()->get('foo'));
    }

    public function testGetMethod()
    {
        $_SERVER['REQUEST_METHOD'] = 'POST';
        $_SERVER['HTTP_X_HTTP_METHOD_OVERRIDE'] = 'PUT';

        $request = new Request();
        $this->assertSame('PUT', $request->getMethod());

        $request->setMethod('DELETE');
        $this->assertSame('DELETE', $request->getMethod());
    }

    public function testGetPath()
    {
        $_SERVER['PATH_INFO'] = '/foo';
        $request = new Request();

        $this->assertSame('/foo', $request->getPath());
    }

    public function testGetParamsQuery()
    {
        $_GET['foo'] = 'bar';
        $request = new Request();
        $this->assertSame('bar', $request->getParamsQuery()->get('foo'));
    }

    public function testGetParamsPost()
    {
        $_POST['foo'] = 'bar';
        $request = new Request();
        $this->assertSame('bar', $request->getParamsPost()->get('foo'));
    }

    /**
     * @dataProvider dataProviderTestParamsFile
     */
    public function testGetParamsFile($files, $expected)
    {
        $_FILES = $files;
        $request = new Request();
        $this->assertSame($expected, $request->getParamsFile()->toArray());
    }

    public function dataProviderTestParamsFile()
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

    public function testReferrer()
    {
        $_SERVER['HTTP_REFERER'] = '/foo';
        $request = new Request();
        $this->assertSame('/foo', $request->getReferrer());
    }

    public function testServerName()
    {
        $_SERVER['SERVER_NAME'] = 'example.com';
        $request = new Request();
        $this->assertSame('example.com', $request->getServerName());
    }

    public function testHost()
    {
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SERVER_PORT'] = 8080;

        $request = new Request();
        $this->assertSame('example.com', $request->getHost());
        $this->assertSame('example.com:8080', $request->getHost(true));
    }

    public function testUrl()
    {
        $_SERVER['HTTPS'] = 'on';
        $_SERVER['HTTP_HOST'] = 'example.com';
        $_SERVER['SERVER_PORT'] = 8080;
        $_SERVER['REQUEST_URI'] = '/foo/bar';

        $request = new Request();
        $this->assertSame('https://example.com:8080/foo/bar', $request->getUrl());
        $this->assertSame('/foo/bar', $request->getUrl(false));
    }

    public function testGetClientIp()
    {
        $_SERVER['REMOTE_ADDR'] = '192.168.1.1';

        $request = new Request();
        $this->assertSame('192.168.1.1', $request->getClientIp());
    }

    public function testGetUserAgent()
    {
        $_SERVER['USER_AGENT'] = 'ua';

        $request = new Request();
        $this->assertSame('ua', $request->getUserAgent());
    }

    public function testAccepts()
    {
        $_SERVER['HTTP_ACCEPT'] = 'foo,bar';

        $request = new Request();
        $this->assertSame(['foo', 'bar'], $request->getAccepts());
        $this->assertTrue($request->isAccept('foo'));
    }

    public function testGetContentType()
    {
        $_SERVER['HTTP_CONTENT_TYPE'] = 'application/json';

        $request = new Request();
        $this->assertSame('application/json', $request->getContentType());
    }

    public function testIsXhr()
    {
        $_SERVER['HTTP_X_REQUESTED_WITH'] = 'XMLHttpRequest';
        $request = new Request();
        $this->assertTrue($request->isXhr());
    }
}