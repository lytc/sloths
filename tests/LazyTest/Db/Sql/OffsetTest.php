<?php

namespace LazyTest\Db\Sql;

use Lazy\Db\Sql\Offset;

/**
 * @covers Lazy\Db\Sql\Offset
 */
class OffsetTest extends \PHPUnit_Framework_TestCase
{

    public function testGetSetOffset()
    {
        $offset = new Offset(10);
        $this->assertSame('OFFSET 10', $offset->toString());
        $this->assertSame(10, $offset->offset());

        $offset = new Offset();
        $offset->offset(10);
        $this->assertSame('OFFSET 10', $offset->toString());
        $this->assertSame(10, $offset->offset());
    }

    public function testOffsetShouldReturnAnEmptyStringWhenOffsetNotSet()
    {
        $offset = new Offset();
        $this->assertSame('', $offset->toString());
        $this->assertNull($offset->offset());
    }

    public function testReset()
    {
        $offset = new Offset(10);
        $this->assertSame(10, $offset->offset());
        $offset->reset();
        $this->assertNull($offset->offset());
    }
}