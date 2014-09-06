<?php

namespace SlothsTest\Http\Client;

use Sloths\Http\Client\Request;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\Client\Request
 */
class RequestTest extends TestCase
{
    public function testNewWithArgs()
    {
        $request = new Request(Request::METHOD_POST, 'http://foo.com', ['foo' => 'bar'], ['Foo' => 'bar']);
        $this->assertSame('http://foo.com', $request->getUrl());
        $this->assertSame(Request::METHOD_POST, $request->getMethod());
        $this->assertSame(['foo' => 'bar'], $request->getParams()->toArray());
        $this->assertSame(['Foo' => 'bar'], $request->getHeaders()->toArray());
    }
}