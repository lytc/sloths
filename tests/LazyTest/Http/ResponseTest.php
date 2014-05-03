<?php

use Lazy\Http\Response;

class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetStatusCode()
    {
        $response = new Response();
        $response->setStatusCode(404);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testHeaderMethods()
    {
        $response = new Response();
        $response->setHeaders(['foo' => 'bar']);
        $this->assertSame(['foo' => 'bar'], $response->getHeaders());

        $response->setHeaders(['bar' => 'baz']);
        $this->assertSame(['bar' => 'baz'], $response->getHeaders());

        $response->addHeaders(['foo' => 'bar']);
        $this->assertSame(['bar' => 'baz', 'foo' => 'bar'], $response->getHeaders());

        $response->addHeaders(['baz' => 'qux']);
        $this->assertSame(['bar' => 'baz', 'foo' => 'bar', 'baz' => 'qux'], $response->getHeaders());

        $response->setHeader('bar', 'xxx');
        $this->assertSame(['bar' => 'xxx', 'foo' => 'bar', 'baz' => 'qux'], $response->getHeaders());

        $this->assertSame('xxx', $response->getHeader('bar'));
        $this->assertNull($response->getHeader('non existing header'));

        $response->setHeader('yyy');
        $this->assertSame(['bar' => 'xxx', 'foo' => 'bar', 'baz' => 'qux', 'yyy'], $response->getHeaders());
    }

    public function testSetAndGetBody()
    {
        $response = new Response();
        $response->setBody('foo');
        $this->assertSame('foo', $response->getBody());
    }

    public function testSend()
    {
        $response = new Response();
        $response->setBody('foo');

        $this->expectOutputString('foo');
        $response->send();

        $this->assertSame(200, http_response_code());
        $this->assertSame('text/html', $response->getHeader('Content-Type'));
    }

    public function testSendJsonData()
    {
        $data = ['foo' => 'bar'];
        $response = new Response();
        $response->setBody($data);

        $this->expectOutputString(json_encode($data));
        $response->send();

        $this->assertSame(200, http_response_code());
        $this->assertSame('application/json', $response->getHeader('Content-Type'));
    }

    public function testRedirect()
    {
        $response = new Response();
        $response->redirect('/foo');
        $this->assertSame('/foo', $response->getHeader('Location'));
    }
}