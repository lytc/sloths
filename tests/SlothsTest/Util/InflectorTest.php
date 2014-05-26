<?php

namespace SlothsTest\Util;

use Sloths\Util\Inflector;
use SlothsTest\TestCase;

class InflectorTest extends TestCase
{
    public function testUnderscore()
    {
        $this->assertSame('foo_bar_baz', Inflector::underscore('fooBarBaz'));
    }
}