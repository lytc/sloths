<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Sql\Order;

/**
 * @covers Lazy\Db\Sql\Order
 */
class OrderTest extends \PHPUnit_Framework_TestCase
{
    public function testWithStringArgument()
    {
        $order = new Order('foo');
        $this->assertSame(array('foo' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo ASC', $order->toString());

        $order = new Order();
        $order->order('foo');
        $this->assertSame(array('foo' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo ASC', $order->toString());

        $order = new Order();
        $order->order('foo, bar DESC ,   baz ');
        $this->assertSame(array('foo' => 'ASC', 'bar' => 'DESC', 'baz' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo ASC, bar DESC, baz ASC', $order->toString());

        $order = new Order('foo', 'bar DESC', 'baz DESC, qux');
        $this->assertSame(array('foo' => 'ASC', 'bar' => 'DESC', 'baz' => 'DESC', 'qux' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo ASC, bar DESC, baz DESC, qux ASC', $order->toString());

        $order = new Order();
        $order->order('foo', 'bar DESC', 'baz DESC, qux');
        $this->assertSame(array('foo' => 'ASC', 'bar' => 'DESC', 'baz' => 'DESC', 'qux' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo ASC, bar DESC, baz DESC, qux ASC', $order->toString());

    }

    public function testWithArrayArgument()
    {
        $order = new Order();
        $order->order(array('foo'));
        $this->assertSame(array('foo' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo ASC', $order->toString());

        $order = new Order();
        $order->order(array('foo', 'bar DESC', 'baz'));
        $this->assertSame(array('foo' => 'ASC', 'bar' => 'DESC', 'baz' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo ASC, bar DESC, baz ASC', $order->toString());

        $order = new Order(array('foo' => 'DESC', 'bar'));
        $this->assertSame(array('foo' => 'DESC', 'bar' => 'ASC'), $order->order());
        $this->assertSame('ORDER BY foo DESC, bar ASC', $order->toString());
    }


    public function testItShouldReturnAnEmptyStringWhenHasNoOrder()
    {
        $order = new Order();
        $this->assertSame('', $order->toString());
    }

    public function testReset()
    {
        $order = new Order('foo');
        $this->assertSame(array('foo' => 'ASC'), $order->order());
        $order->reset();
        $this->assertSame(array(), $order->order());
    }
}