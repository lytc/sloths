<?php

use Sloths\Http\Response;

/**
 * @covers \Sloths\Http\Response<extended>
 */
class ResponseTest extends \PHPUnit_Framework_TestCase
{
    public function testSetAndGetStatusCode()
    {
        $response = new Response();
        $response->setStatusCode(404);
        $this->assertSame(404, $response->getStatusCode());
    }

    public function testSetAndGetBody()
    {
        $response = new Response();
        $response->setBody('foo');
        $this->assertSame('foo', $response->getBody());
    }

    public function testSend()
    {
        $response = $this->getMock('Sloths\Http\Response', ['sendHeader']);
        $response->expects($this->once())->method('sendHeader')->with('Content-Type', 'text/html');
        $response->setBody('foo');

        $this->expectOutputString('foo');
        $response->send();

        $this->assertSame(200, http_response_code());
    }

    public function testSendJsonData()
    {
        $data = ['foo' => 'bar'];
        $response = $this->getMock('Sloths\Http\Response', ['sendHeader']);
        $response->expects($this->once())->method('sendHeader')->with('Content-Type', 'application/json');
        $response->setBody($data);

        $this->expectOutputString(json_encode($data));
        $response->send();

        $this->assertSame(200, http_response_code());
    }

    public function testRedirect()
    {
        $response = $this->getMock('Sloths\Http\Response', ['sendHeader']);
        $response->expects($this->once())->method('sendHeader')->with('Location', '/foo');
        $response->redirect('/foo');
        $response->send();
    }
}