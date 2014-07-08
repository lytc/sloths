<?php

namespace SlothsTest\Authentication\Adapter;

use Sloths\Authentication\Adapter\Callback;
use Sloths\Authentication\Result;
use SlothsTest\TestCase;

class CallbackTest extends TestCase
{
    /**
     * @param $code
     * @dataProvider dataProviderReturnErrorCode
     */
    public function testReturnErrorCode($code)
    {
        $adapter = new Callback(function() use ($code, &$that) {
            $that = $this;
            return $code;
        });

        $result = $adapter->authenticate();
        $this->assertSame($adapter, $that);
        $this->assertInstanceOf('Sloths\Authentication\Result', $result);
        $this->assertSame($code, $result->getCode());


        $identity = 'identity';
        $credential = 'credential';

        $callback = $this->getMock('foo', ['callback']);

        $adapter = new Callback([$callback, 'callback']);
        $adapter->setIdentity($identity);
        $adapter->setCredential($credential);

        $callback->expects($this->once())->method('callback')->with($adapter, $identity, $credential)->willReturn($code);

        $adapter->authenticate();

        $this->assertInstanceOf('Sloths\Authentication\Result', $result);
        $this->assertSame($code, $result->getCode());
    }

    public function dataProviderReturnErrorCode()
    {
        return [
            [Result::ERROR_FAILURE],
            [Result::ERROR_IDENTITY_NOT_FOUND],
            [Result::ERROR_CREDENTIAL_INVALID]
        ];
    }

    public function testReturnData()
    {
        $adapter = new Callback(function() {
            return 'foo';
        });

        $result = $adapter->authenticate();
        $this->assertInstanceOf('Sloths\Authentication\Result', $result);
        $this->assertSame(Result::SUCCESS, $result->getCode());
        $this->assertSame('foo', $result->getData());
    }

    public function testReturnResultInstance()
    {
        $result = new Result(1, 'foo');
        $adapter = new Callback(function() use ($result) {
            return $result;
        });

        $this->assertSame($result, $adapter->authenticate());
    }
}