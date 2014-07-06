<?php

namespace SlothsTest\Util;

use Sloths\Util\StringUtils;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Util\StringUtils
 */
class StringUtilsTest extends TestCase
{
    /**
     * @dataProvider randomDataProvider
     */
    public function testRandom($format, $flags = StringUtils::RANDOM_ALNUM)
    {
        $str = StringUtils::random(100, $flags);
        $this->assertSame(100, strlen($str));
        $this->assertRegExp($format, $str);
    }

    public function randomDataProvider()
    {
        return [
            ['/^[a-z]+$/', StringUtils::RANDOM_ALPHA_LOWER],
            ['/^[A-Z]+$/', StringUtils::RANDOM_ALPHA_UPPER],
            ['/^\d+$/', StringUtils::RANDOM_NUMERIC],
            ['/^[\W]+$/', StringUtils::RANDOM_SPECIAL_CHAR],
            ['/^[a-zA-Z]+$/', StringUtils::RANDOM_ALPHA],
            ['/^[\w]+$/', StringUtils::RANDOM_ALNUM],
            ['/^[\w\W]+$/', StringUtils::RANDOM_ALL],
            ['/^[a-z0-9]+$/', StringUtils::RANDOM_ALPHA_LOWER|StringUtils::RANDOM_NUMERIC],
            ['/^[A-Z0-9]+$/', StringUtils::RANDOM_ALPHA_UPPER|StringUtils::RANDOM_NUMERIC],
            ['/^[\W0-9]+$/', StringUtils::RANDOM_SPECIAL_CHAR|StringUtils::RANDOM_NUMERIC],

        ];
    }

    public function testGetNamespace()
    {
        $this->assertSame('Foo\Bar', StringUtils::getNamespace('Foo\Bar\Baz'));
        $this->assertNull(StringUtils::getNamespace('Foo'));
    }

    public function testGetClassNameWithoutNamspace()
    {
        $this->assertSame('Baz', StringUtils::getClassNameWithoutNamespaceName('Foo\Bar\Baz'));
        $this->assertSame('Foo', StringUtils::getClassNameWithoutNamespaceName('Foo'));
    }

    /**
     * @dataProvider formatDataProvider
     */
    public function testFormat($expected, $str, $data = [])
    {
        $this->assertSame($expected, StringUtils::format($str, $data));
    }

    public function formatDataProvider()
    {
        return [
            ['foo', ':name', ['name' => 'foo']],
            ['foo', ':0', ['foo']],
            ['foo foo', ':name :name', ['name' => 'foo']],
            ['foo ', ':name :foo', ['name' => 'foo']],
            ['foo ', ':name :namefoo', ['name' => 'foo']],
            ['foo :name', ':name ::name', ['name' => 'foo']],
            ['::', '::::'],
            [':foo', ':::name', ['name' => 'foo']],
            ['::name', '::::name', ['name' => 'foo']],
            ['', ':name', ['name' => null]],
        ];
    }
}