<?php

namespace Sloths\Http\Message;

use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Message\Headers
 */
class HeadersTest extends TestCase
{
    public function testNameCaseInsensitive()
    {
        $headers = new Headers();
        $headers->foo = 'bar';
        $this->assertSame('bar', $headers->foo);
        $this->assertSame('bar', $headers->Foo);
        $this->assertSame('bar', $headers->FOO);

        $headers['foo-bar'] = 'baz';
        $this->assertSame('baz', $headers['foo-bar']);
        $this->assertSame('baz', $headers['FOO-BAR']);
        $this->assertSame('baz', $headers['Foo-Bar']);

        $headers->remove('foo');
        $this->assertNull($headers->foo);
    }

    public function testGetLine()
    {
        $headers = new Headers(['foo' => 'bar']);
        $this->assertSame('Foo: bar', $headers->getLine('Foo'));
    }

    public function testGetLines()
    {
        $headers = new Headers(['foo' => 'bar', 'foo-bar' => 'baz']);
        $expected = ['Foo: bar', 'Foo-Bar: baz'];
        $this->assertSame($expected, $headers->getLines());
    }

    public function testToString()
    {
        $headers = new Headers(['foo' => 'bar', 'foo-bar' => 'baz']);
        $expected = "Foo: bar\r\nFoo-Bar: baz";
        $this->assertSame($expected, $headers->toString());
    }
}