<?php

namespace SlothsTest\Http;

use Sloths\Http\Client;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Client
 */
class ClientTest extends TestCase
{
    const WEB_SERVER_HOST = '0.0.0.0';
    const WEB_SERVER_PORT = 8008;
    const TIMEOUT = 1;

    protected static $pid;

    public static function setUpBeforeClass()
    {
        $command = sprintf(
            'php -S %s:%d -t %s >/dev/null 2>&1 & echo $!',
            self::WEB_SERVER_HOST,
            self::WEB_SERVER_PORT,
            __DIR__ . '/fixtures/scripts'
        );

        $output = array();
        exec($command, $output);
        self::$pid = (int) $output[0];

        self::ensureReadyToConnect();
    }

    public static function ensureReadyToConnect()
    {
        $connected = false;
        $time = microtime(true);

        set_error_handler(function() {});
        while (microtime(true) - $time <= self::TIMEOUT) {
            if (file_get_contents(self::getScriptUrl('status.php')) === 'ok') {
                $connected = true;
                break;
            }
        }

        restore_error_handler();

        if (!$connected) {
            throw new \RuntimeException('Could not connect to web server');
        }
    }

    public static function tearDownAfterClass()
    {
        exec('kill ' . self::$pid);
    }

    protected static function getScriptUrl($script)
    {
        return sprintf('http://%s:%s/%s', self::WEB_SERVER_HOST, self::WEB_SERVER_PORT, $script);
    }

    public function testNewInstanceWithoutArgs()
    {
        $client = new Client();
        $this->assertInstanceOf('Sloths\Http\Client\Request', $client->getRequest());
    }

    public function testNewInstanceWithRequestAndResponseObject()
    {
        $request = new Client\Request();
        $client = new Client($request);

        $this->assertSame($request, $client->getRequest());
    }

    public function testNewInstanceWithUri()
    {
        $client = new Client('http://example.com/foo');
        $this->assertSame('http://example.com/foo', $client->getRequest()->getUrl());
    }

    public function testGetCurl()
    {
        $client = new Client();
        $this->assertSame($client->getCurl(), $client->getCurl());
    }

    public function testSendSimpleGet()
    {
        $url = self::getScriptUrl('simple-get.php');
        $client = new Client($url);
        $response = $client->send();

        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('foo', $response->getBody());
    }

    public function testSendWithHeader()
    {
        $url = self::getScriptUrl('test-header.php');
        $client = new Client($url);
        $client->getRequest()->getHeaders()->set('Foo', 'bar');
        $response = $client->send();

        $this->assertSame('bar', $response->getBody());
    }

    public function testSendWithQuery()
    {
        $url = self::getScriptUrl('test-query.php');
        $client = new Client($url);
        $client->getRequest()->setQueryParams(['foo' => 'bar']);
        $this->assertSame('bar', $client->send()->getBody());
    }

    public function testSendWithPostParams()
    {
        $url = self::getScriptUrl('test-post-params.php');
        $client = new Client($url);
        $client->getRequest()->setMethod(Client\Request::METHOD_POST)->setParams(['foo' => 'bar']);
        $this->assertSame('bar', $client->send()->getBody());
    }

    public function testSendPostRawBody()
    {
        $url = self::getScriptUrl('test-post-raw-body.php');
        $client = new Client($url);
        $client->getRequest()->setMethod(Client\Request::METHOD_POST)->setBody('foo');
        $this->assertSame('foo', $client->send()->getBody());
    }

    public function testMethodSendWithRequestParam()
    {
        $request = new Client\Request();
        $client = $this->getMock('Sloths\Http\Client', ['doRequest']);
        $client->send($request);
        $this->assertSame($request, $client->getRequest());
    }

    public function testMethodSendWithUrlParam()
    {
        $client = $this->getMock('Sloths\Http\Client', ['doRequest']);
        $client->send('http://example.com');
        $this->assertSame('http://example.com', $client->getRequest()->getUrl());
    }

    public function testMethodGet()
    {
        $client = new Client();
        $client->get('http://example.com', ['foo' => 'bar'], ['Foo' => 'bar']);
        $request = $client->getRequest();

        $this->assertSame('http://example.com?foo=bar', $request->getUrl());
        $this->assertSame(Client\Request::METHOD_GET, $request->getMethod());
        $this->assertSame(['foo' => 'bar'], $request->getQueryParams()->toArray());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }

    public function testMethodHead()
    {
        $client = new Client();
        $client->head('http://example.com', ['foo' => 'bar'], ['Foo' => 'bar']);
        $request = $client->getRequest();

        $this->assertSame('http://example.com?foo=bar', $request->getUrl());
        $this->assertSame(Client\Request::METHOD_HEAD, $request->getMethod());
        $this->assertSame(['foo' => 'bar'], $request->getQueryParams()->toArray());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }

    public function testMethodPost()
    {
        $client = new Client();
        $client->post('http://example.com', ['foo' => 'bar'], ['Foo' => 'bar']);
        $request = $client->getRequest();

        $this->assertSame('http://example.com', $request->getUrl());
        $this->assertSame(Client\Request::METHOD_POST, $request->getMethod());
        $this->assertSame(['foo' => 'bar'], $request->getParams()->toArray());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }

    public function testMethodPut()
    {
        $client = new Client();
        $client->put('http://example.com', 'foo', ['Foo' => 'bar']);
        $request = $client->getRequest();

        $this->assertSame('http://example.com', $request->getUrl());
        $this->assertSame(Client\Request::METHOD_PUT, $request->getMethod());
        $this->assertSame('foo', $request->getBody());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }

    public function testMethodPatch()
    {
        $client = new Client();
        $client->patch('http://example.com', 'foo', ['Foo' => 'bar']);
        $request = $client->getRequest();

        $this->assertSame('http://example.com', $request->getUrl());
        $this->assertSame(Client\Request::METHOD_PATCH, $request->getMethod());
        $this->assertSame('foo', $request->getBody());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }

    public function testMethodDelete()
    {
        $client = new Client();
        $client->delete('http://example.com', ['foo' => 'bar'], ['Foo' => 'bar']);
        $request = $client->getRequest();

        $this->assertSame('http://example.com?foo=bar', $request->getUrl());
        $this->assertSame(Client\Request::METHOD_DELETE, $request->getMethod());
        $this->assertSame(['foo' => 'bar'], $request->getQueryParams()->toArray());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }

    public function testMethodOptions()
    {
        $client = new Client();
        $client->options('http://example.com', ['foo' => 'bar'], ['Foo' => 'bar']);
        $request = $client->getRequest();

        $this->assertSame('http://example.com?foo=bar', $request->getUrl());
        $this->assertSame(Client\Request::METHOD_OPTIONS, $request->getMethod());
        $this->assertSame(['foo' => 'bar'], $request->getQueryParams()->toArray());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }

    public function testMethodPostWithParamStringShouldSetBody()
    {
        $client = new Client();
        $client->post('http://example.com', 'bar');
        $request = $client->getRequest();

        $this->assertSame('bar', $request->getBody());
    }

    public function testUpload()
    {
        $url = self::getScriptUrl('test-upload.php');
        $file = __DIR__ . '/fixtures/files/image.jpg';

        $client = new Client();
        $client->upload($url, $file);
        $response = $client->send();

        $this->assertSame(filesize($file), (int) $response->getBody());
    }

    public function testUploadWithParams()
    {
        $url = self::getScriptUrl('test-upload-with-params.php');
        $file = __DIR__ . '/fixtures/files/image.jpg';

        $client = new Client();
        $client->upload($url, $file);
        $client->getRequest()->setParams(['foo' => 'bar']);
        $response = $client->send();

        $this->assertSame('bar-' . filesize($file), $response->getBody());
    }

    public function testUploadWithHeaders()
    {
        $client = new Client();
        $client->upload('http://example.com', 'bar', ['Foo' => 'bar']);
        $this->assertSame('bar', $client->getRequest()->getHeaders()->get('Foo'));
    }

    public function testPutRawBody()
    {
        $url = self::getScriptUrl('test-put-raw-body.php');
        $body = 'foo';

        $client = new Client();
        $client->put($url, $body);

        $response = $client->send();
        $this->assertSame('PUT-foo', $response->getBody());
    }

    public function testPutFile()
    {
        $url = self::getScriptUrl('test-put-file.php');
        $file = __DIR__ . '/fixtures/files/image.jpg';
        $body = fopen($file, 'r');

        $client = new Client();
        $client->put($url, $body);

        $response = $client->send();

        $this->assertSame('PUT-' . filesize($file), $response->getBody());

        fclose($body);
    }

    public function testHead()
    {
        $url = self::getScriptUrl('test-head.php');
        $client = new Client();
        $client->head($url, ['foo' => 'bar'], ['bar' => 'baz']);

        $response = $client->send();
        $this->assertSame(['foo' => 'bar', 'bar' => 'baz'], json_decode($response->getHeaders()->get('Response'), true));
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testInvalidRequestShouldThrowAnException()
    {
        $client = new Client();
        $client->send();
    }
}