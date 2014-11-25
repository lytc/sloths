<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\Callback;

/**
 * @covers Sloths\Validation\Validator\Callback
 * @covers Sloths\Validation\Validator\ValidatorTrait
 */
class CallbackTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $callback, $input)
    {
        $this->assertSame($expected, (new Callback($callback))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, function($input) {return $input === 1;}, 1],
            [false, function($input) {return $input !== 1;}, 1],
        ];
    }

    public function testWithMessage()
    {
        $validator =  new Callback(function() {
            return 'foo';
        });

        $validator->validate(1);
        $this->assertSame('foo', $validator->getMessage());

        $validator =  new Callback(function() {
            return [
                'Foo :0',
                'foo'
            ];
        });

        $validator->validate(1);
        $this->assertSame('Foo foo', $validator->getMessage());
    }
}