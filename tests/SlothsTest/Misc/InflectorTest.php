<?php

namespace SlothsTest\Misc;

use Sloths\Misc\Inflector;
use SlothsTest\TestCase;

class InflectorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($method, $str, $expected)
    {
        $this->assertSame($expected, call_user_func('Sloths\Misc\Inflector::' . $method, $str));
    }

    public function dataProvider()
    {
        return [
            ['classify', 'foo-bar_baz.qux', 'FooBarBazQux'],
            ['classify', ' foo  bar ', 'FooBar'],
            ['camelize', 'foo-bar_baz.qux', 'fooBarBazQux'],
            ['camelize', ' foo  bar ', 'fooBar'],
            ['underscore', 'FooBar', 'foo_bar'],
            ['underscore', 'foo-bar', 'foo_bar'],
            ['underscore', ' Foo-bar Baz.Qux', 'foo_bar_baz_qux'],
            ['dasherize', 'FooBar_Baz.Qux', 'foo-bar-baz-qux'],
            ['dasherize', ' Foo  Bar ', 'foo-bar'],
            ['titleize', 'foo bar_baz-quxWot', 'Foo Bar Baz Qux Wot'],
        ];
    }
}