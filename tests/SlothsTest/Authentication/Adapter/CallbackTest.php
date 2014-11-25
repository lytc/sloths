<?php

namespace SlothsTest\Authentication\Adapter;

use Sloths\Authentication\Adapter\Callback;
use Sloths\Authentication\Result;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Authentication\Adapter\Callback
 */
class CallbackTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($callback, $expectedCode)
    {
        $adapter = new Callback($callback);
        $result = $adapter->authenticate();
        $this->assertSame($expectedCode, $result->getCode());
    }

    public function dataProvider()
    {
        return [
            [function () {return new Result(Result::SUCCESS);}, Result::SUCCESS],
            [function () {return Result::SUCCESS;}, Result::SUCCESS],
            [function () {return Result::ERROR_FAILURE;}, Result::ERROR_FAILURE],
            [function () {return Result::ERROR_IDENTITY_NOT_FOUND;}, Result::ERROR_IDENTITY_NOT_FOUND],
            [function () {return Result::ERROR_CREDENTIAL_INVALID;}, Result::ERROR_CREDENTIAL_INVALID],
        ];
    }
}