<?php

namespace SlothsTest\Validation\Validator;

use SlothsTest\TestCase;
use Sloths\Validation\Validator\CharacterBetween;

/**
 * @covers Sloths\Validation\Validator\CharacterBetween
 * @covers Sloths\Validation\Validator\ValidatorTrait
 */
class CharacterBetweenTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $min, $max, $input)
    {
        $this->assertSame($expected, (new CharacterBetween($min, $max))->validate($input));
    }

    public function dataProvider()
    {
        return [
            [true, 'a', 'c', 'b'],
            [true, 'a', 'a', 'a'],
            [true, 'a', 'b', 'b'],
            [false, 'a', 'b', 'c'],
            [false, 'a', 'b', 1]
        ];
    }

    public function testMessage()
    {
        $this->assertSame('must be a character between a and c', (new CharacterBetween('a', 'c'))->getMessage());
    }
}