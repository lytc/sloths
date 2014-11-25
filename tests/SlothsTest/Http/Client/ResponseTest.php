<?php

namespace SlothsTest\Http\Client;

use Sloths\Http\Client\Response;
use Sloths\Http\Client;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Client\Response
 */
class ResponseTest extends TestCase
{
    public function test()
    {
        $headers = "HTTP/1.1 200 OK\r\nServer: foo.com\r\nContent-Type: text/html\r\nFoo: bar\r\n";
        $body = '{"foo": "bar"}';
        $output = $headers . $body;

        $info = [
            'content_type' => 'text/html',
            'http_code' => 200,
            'header_size' => strlen($headers)
        ];

        $response = new Response($info, $output);
        $this->assertSame('1.1', $response->getProtocolVersion());
        $this->assertSame(200, $response->getStatusCode());
        $this->assertSame('text/html', $response->getHeaders()->get('Content-Type'));
        $this->assertSame($info, $response->getInfo());
        $this->assertSame(['Server' => 'foo.com', 'Content-Type' => 'text/html', 'Foo' => 'bar'], $response->getHeaders()->toArray());
        $this->assertTrue($response->getHeaders()->has('Foo'));
        $this->assertSame('bar', $response->getHeaders()->get('Foo'));
        $this->assertSame($body, $response->getBody());

        $this->assertSame(['foo' => 'bar'], $response->toJson(true));
    }

    public function testToXml()
    {
        $response = $this->getMock('Sloths\Http\Client\Response', ['getBody'], [], '', false);
        $body = "<root><foo>bar</foo></root>";
        $response->expects($this->once())->method('getBody')->willReturn($body);

        $xml = $response->toXml();
        $this->assertSame('bar', (string) $xml->foo);
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testToJsonShouldThrowAnExceptionIfParseInvalidJsonData()
    {
        $response = $this->getMock('Sloths\Http\Client\Response', ['getBody'], [], '', false);
        $response->expects($this->once())->method('getBody')->willReturn('foo');
        $response->toJson();
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testToXmlShouldThrowAnExceptionIfParseInvalidXmlData()
    {
        $response = $this->getMock('Sloths\Http\Client\Response', ['getBody'], [], '', false);
        $response->expects($this->once())->method('getBody')->willReturn('foo');
        $response->toXml();
    }
}