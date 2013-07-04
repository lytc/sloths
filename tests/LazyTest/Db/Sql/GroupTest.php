<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Sql\Group;

/**
 * @covers Lazy\Db\Sql\Group
 */
class GroupTest extends \PHPUnit_Framework_TestCase
{
    public function testWithStringArgument()
    {
        $group = new Group('foo');
        $this->assertSame(array('foo' => 'foo'), $group->group());
        $this->assertSame('GROUP BY foo', $group->toString());

        $group = new Group();
        $group->group('foo');
        $this->assertSame(array('foo' => 'foo'), $group->group());
        $this->assertSame('GROUP BY foo', $group->toString());

        $group = new Group('foo, bar  ,  baz ');
        $this->assertSame(array('foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz'), $group->group());
        $this->assertSame('GROUP BY foo, bar, baz', $group->toString());

    }

    public function testWithArrayArgument()
    {
        $group = new Group(array('foo'));
        $this->assertSame(array('foo' => 'foo'), $group->group());
        $this->assertSame('GROUP BY foo', $group->toString());

        $group = new Group(array('foo', 'bar'));
        $this->assertSame(array('foo' => 'foo', 'bar' => 'bar'), $group->group());
        $this->assertSame('GROUP BY foo, bar', $group->toString());
    }

    public function testWithStringAndArrayArguments()
    {
        $group = new Group('foo, bar', array('baz', 'qux'));
        $this->assertSame(array('foo' => 'foo', 'bar' => 'bar', 'baz' => 'baz', 'qux' => 'qux'), $group->group());
        $this->assertSame('GROUP BY foo, bar, baz, qux', $group->toString());
    }

    public function testItShouldReturnAnEmptyStringWhenHasNoGroup()
    {
        $group = new Group();
        $this->assertSame('', $group->toString());
    }

    public function testReset()
    {
        $group = new Group('foo');
        $this->assertSame(array('foo' => 'foo'), $group->group());
        $group->reset();
        $this->assertSame(array(), $group->group());
    }
}