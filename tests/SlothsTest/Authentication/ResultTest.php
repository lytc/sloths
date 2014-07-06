<?php

namespace SlothsTest\Authentication;

use Sloths\Authentication\Result;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Authentication\Result
 */
class ResultTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expectedSuccess, $expectedError, $expectedMessage, $code, $data, $message = '')
    {
        $result = new Result($code, $data, $message);

        $this->assertSame($code, $result->getCode());
        $this->assertSame($data, $result->getData());
        $this->assertSame($expectedMessage, $result->getMessage());
        $this->assertSame($expectedSuccess, $result->isSuccess());
        $this->assertSame($expectedError, $result->isError());
    }

    public function dataProvider()
    {
        return [
            [true, false, null, 1, 'foo', 'bar'],
            [false, true, 'bar', 0, 'foo', 'bar'],
            [false, true, 'bar', -1, 'foo', 'bar'],
            [false, true, 'bar', -2, 'foo', 'bar'],
        ];
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidCodeShouldThrowAnException()
    {
        new Result(2, 'foo');
    }
}