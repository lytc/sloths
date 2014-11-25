<?php

namespace SlothsTest\Authentication;

use SlothsTest\TestCase;
use Sloths\Authentication\Result;


/**
 * @covers Sloths\Authentication\Result
 */
class ResultTest extends TestCase
{
    public function test()
    {
        $result = new Result(Result::ERROR_IDENTITY_NOT_FOUND);
        $this->assertSame(Result::ERROR_IDENTITY_NOT_FOUND, $result->getCode());
        $this->assertSame('Identity not found', $result->getMessage());

        $this->assertFalse($result->isSuccess());
        $this->assertTrue($result->isError());

        $data = 'foo';
        $result = new Result(Result::SUCCESS, $data);
        $this->assertSame($data, $result->getData());
    }

    public function testCustomMessages()
    {
        $result = new Result('foo', '', ['foo' => 'foo message']);
        $this->assertSame('foo message', $result->getMessage());
    }
}