<?php

namespace SlothsTest\Http;

use Sloths\Http\Headers;
use Sloths\Http\MessageInterface;
use Sloths\Http\MessageTrait;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Http\MessageTrait
 */
class MessageTraitTest extends TestCase
{
    public function test()
    {
        $headers = new Headers();
        $message = new StubMessage();
        $message
            ->setProtocolVersion(MessageInterface::PROTOCOL_VERSION_1_0)
            ->setHeaders($headers)
            ->setBody('body');

        $this->assertSame(MessageInterface::PROTOCOL_VERSION_1_0, $message->getProtocolVersion());
        $this->assertSame($headers, $message->getHeaders());
        $this->assertSame('body', $message->getBody());
    }

    public function testDefaultHeadersInstance()
    {
        $message = new StubMessage();
        $this->assertInstanceOf('Sloths\Http\Headers', $message->getHeaders());
    }
}

class StubMessage
{
    use MessageTrait;
}