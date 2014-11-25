<?php

namespace SlothsTest\Pagination\Adapter;

use Sloths\Pagination\Adapter\ModelCollection;
use SlothsTest\TestCase;

use Sloths\Pagination\Adapter\ArrayAdapter;

/**
 * @cover Sloths\Pagination\Adapter\ModelCollection
 */
class ModelCollectionTest extends TestCase
{
    public function test()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['foundRows', 'limit'], [], '', false);
        $collection->expects($this->once())->method('foundRows')->willReturn(100);
        $collection->expects($this->once())->method('limit')->with(20, 10);

        $adapter = new ModelCollection($collection);
        $this->assertSame($collection, $adapter->getModelCollection());

        $this->assertSame(100, $adapter->count());
        $this->assertSame($collection, $adapter->getRange(10, 20));
    }
}