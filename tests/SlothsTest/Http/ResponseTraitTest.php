<?php

namespace SlothsTest\Http;

use Sloths\Http\ResponseTrait;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\ResponseTrait
 */
class ResponseTraitTest extends TestCase
{
    public function test()
    {
        $response = new StubResponse();
        $response->setStatusCode(200);
        $this->assertSame('OK', $response->getReasonPhrase());

        $response->setStatusCode(99);
        $this->assertNull($response->getReasonPhrase());

        $response->setReasonPhrase('foo');
        $this->assertSame('foo', $response->getReasonPhrase());
    }
}

class StubResponse
{
    use ResponseTrait;
}