<?php
namespace LazyTest\Util;
use Lazy\Util\String;

class StringTest extends \PHPUnit_Framework_TestCase
{
    public function testCamelize()
    {
        $this->assertSame('fooBarBaz', String::camelize('foo_bar_baz'));
        $this->assertSame('fooBarBaz', String::camelize('foo-bar-baz'));
        $this->assertSame('FooBarBaz', String::camelize(true, 'foo_bar_baz'));
    }
}