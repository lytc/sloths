<?php

namespace SlothsTest\Exception;

use Sloths\Exception\Handler;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Exception\Handler
 */
class HandlerTest extends TestCase
{
    public function testWithExactlyExceptionName()
    {
        $handler = new Handler();
        $handler->add(__NAMESPACE__ . '\\FooException', function($e) use (&$expected) {
            $expected = $e;
        });

        $handle = new \ReflectionMethod('Sloths\Exception\Handler', 'handle');
        $handle->setAccessible(true);
        $handle->invoke($handler, $foo = new FooException());
        $handle->invoke($handler, $bar = new BarException());
        $this->assertSame($foo, $expected);

        $handler->restore()->clear();
    }

    public function testHandleSubException()
    {
        $handler = new Handler();
        $expected = [];
        $handler->add(function(\Exception $e) use (&$expected) {
            $expected[] = $e;
        });

        $handle = new \ReflectionMethod('Sloths\Exception\Handler', 'handle');
        $handle->setAccessible(true);
        $handle->invoke($handler, $foo = new FooException());
        $handle->invoke($handler, $bar = new BarException());

        $this->assertSame($foo, $expected[0]);
        $this->assertSame($bar, $expected[1]);

        $handler->restore()->clear();
    }

    public function testGetInstance()
    {
        $handler = Handler::getInstance();
        $this->assertSame($handler, Handler::getInstance());
    }
}

class FooException extends \Exception
{

}

class BarException extends FooException
{

}