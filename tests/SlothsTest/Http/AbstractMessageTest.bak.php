<?php

namespace SlothsTest\Http;

use Sloths\Http\AbstractMessage;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Http\AbstractMessage
 */
class AbstractMessageTest extends TestCase
{
    public function testSetAndGetHeader()
    {
        $message = new MessageMock();
        $message->setHeader('foo-bar', 'bar');
        $this->assertSame(['Foo-Bar' => 'bar'], $message->getHeaders());
        $this->assertSame('bar', $message->getHeader('foo-bar'));
        $this->assertSame('bar', $message->getHeader('Foo-Bar'));
        $this->assertSame('bar', $message->getHeader('FOO_BAR'));
        $this->assertTrue($message->hasHeader('foo-bar'));
        $this->assertTrue($message->hasHeader('Foo-Bar'));
    }

    public function testMassSetHeader()
    {
        $message = new MessageMock();
        $message->setHeaders(['foo' => 'bar', 'foo-bar' => 'baz']);
        $this->assertSame(['Foo' => 'bar', 'Foo-Bar' => 'baz'], $message->getHeaders());
    }

    public function testRemoveHeader()
    {
        $message = new MessageMock();

        $message->setHeader('foo', 'bar');
        $message->setHeader('foo-bar', 'baz');

        $this->assertTrue($message->hasHeader('foo'));
        $this->assertTrue($message->hasHeader('foo-bar'));

        $message->removeHeader('bar');
        $this->assertFalse($message->hasHeader('bar'));

        $message->removeHeader('Foo-Bar');
        $this->assertFalse($message->hasHeader('foo-bar'));
    }

    public function testResetHeaders()
    {
        $message = new MessageMock();
        $message->setHeaders(['foo' => 'bar']);

        $message->resetHeaders();
        $this->assertSame([], $message->getHeaders());
    }

    public function testGetHeadersAsString()
    {
        $message = new MessageMock();
        $message->setHeaders(['foo' => 'bar', 'foo-bar' => 'baz']);

        $expected = "Foo: bar\r\nFoo-Bar: baz";
        $this->assertSame($expected, $message->getHeaderAsString());
    }

    public function testSetAndGetBody()
    {
        $message = new MessageMock();
        $message->setBody('foo');
        $this->assertSame('foo', $message->getBody());
    }
}

class MessageMock extends AbstractMessage
{

}