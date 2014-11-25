<?php

namespace SlothsTest\Http;

use SlothsTest\TestCase;
use Sloths\Http\Headers;

class HeadersTest extends TestCase
{
    public function test()
    {
        $raw = [
            'foo' => 'foo',
            'foo-bar' => 'foo-bar',
        ];

        $headers = new Headers($raw);
        $this->assertSame(['Foo' => 'foo', 'Foo-Bar' => 'foo-bar'], $headers->toArray());

        $this->assertTrue($headers->has('foo'));
        $this->assertTrue($headers->has('Foo'));
        $this->assertFalse($headers->has('bar'));

        $this->assertSame('foo', $headers->get('Foo'));

        $headers->remove('Foo');
        $this->assertFalse($headers->has('foo'));

        $this->assertSame('Foo-Bar: foo-bar', $headers->getLine('foo-bar'));
        $this->assertSame(['Foo-Bar: foo-bar'], $headers->getLines());
        $this->assertSame('Foo-Bar: foo-bar', $headers->toString());

        $this->assertNull($headers->getLine('non-existing'));
    }
}