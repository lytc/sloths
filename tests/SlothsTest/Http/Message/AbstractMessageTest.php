<?php

namespace SlothsTest\Http\Message;

use Sloths\Http\Message\AbstractMessage;
use Sloths\Http\Message\Headers;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Message\AbstractMessage
 */
class AbstractMessageTest extends TestCase
{
    public function testGetProtocolVersion()
    {
        $message = new Message();
        $this->assertSame(Message::DEFAULT_PROTOCOL_VERSION, $message->getProtocolVersion());
    }

    public function testHeaders()
    {
        $message = new Message();
        $this->assertInstanceOf('Sloths\Http\Message\Headers', $message->getHeaders());

        $headers = new Headers();
        $message->setHeaders($headers);
        $this->assertSame($headers, $message->getHeaders());

        $message->setHeaders(['foo' => 'bar']);
        $this->assertSame(['Foo' => 'bar'], $message->getHeaders()->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidHeaderShouldThrowAnException()
    {
        $messages = new Message();
        $messages->setHeaders('foo');
    }

    public function testBody()
    {
        $message = new Message();
        $message->setBody('foo');
        $this->assertSame('foo', $message->getBody());
    }

    public function test__get()
    {
        $message = new Message();
        $this->assertSame($message->headers, $message->getHeaders());

        $message->setBody('foo');
        $this->assertSame('foo', $message->body);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test__getShouldUndefinedPropertyThrowAnException()
    {
        $message = new Message();
        $message->foobar;
    }

    public function test__set()
    {
        $message = new Message();

        $message->headers = ['Foo' => 'bar'];
        $this->assertSame(['Foo' => 'bar'], $message->getHeaders()->toArray());

        $message->body = 'foo';
        $this->assertSame('foo', $message->getBody());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test__setShouldUndefinedPropertyThrowAnException()
    {
        $message = new Message();
        $message->foobar = 'foo';
    }
}

class Message extends AbstractMessage
{}