<?php

namespace SlothsTest\Validation\Rule;

use Sloths\Validation\Rule\HasAttribute;

class HasAttributeTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $object, $attribute)
    {
        $this->assertSame($expected, (new HasAttribute($attribute))->validate($object));
    }

    public function dataProvider()
    {
        $c = new \stdClass();
        $c->attr1 = 1;
        $c->attr2 = null;
        $c->attr3 = true;
        $c->attr4 = [];
        $c->attr5 = new \stdClass();

        return [
            [true, $c, 'attr1'],
            [true, $c, 'attr2'],
            [true, $c, 'attr3'],
            [true, $c, 'attr4'],
            [true, $c, 'attr5'],
            [false, $c, 'attr6'],
            [false, null, 'foo'],
            [false, true, 'foo'],
            [false, '', 'foo'],
            [false, [], 'foo'],
        ];
    }

    /**
     * @dataProvider invalidKeyDataProvider
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidKeyShouldThrowAnException($key)
    {
        new HasAttribute($key);
    }

    public function invalidKeyDataProvider()
    {
        return [
            [true],
            [false],
            [null],
            [1.1],
            [[]],
            [new \stdClass()]
        ];
    }
}