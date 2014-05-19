<?php

namespace SlothsTest\Util;

use Sloths\Util\StringUtils;
use SlothsTest\TestCase;

class StringUtilsTest extends TestCase
{
    public function testRandom()
    {
        $str = StringUtils::random(10);
        $this->assertSame(10, strlen($str));
    }

    public function testRandomAlphaOnly()
    {
        $str = StringUtils::random(10, false);
        $this->assertSame(1, preg_match('/^([a-zA-Z]+)$/', $str));
    }

    public function testRandomAlphaOnlyWithoutUpperCase()
    {
        $str = StringUtils::random(10, false, false);
        $this->assertSame(1, preg_match('/^([a-zA-Z]+)$/', $str));
    }

    public function testRandomWithSpecialChar()
    {
        $str = StringUtils::random(100, false, false, true);
        $this->assertSame(0, preg_match('/^([a-z]+)$/', $str));
    }
}