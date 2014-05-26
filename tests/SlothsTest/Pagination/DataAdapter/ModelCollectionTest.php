<?php

namespace SlothsTest\Pagination\DataAdapter;

use Sloths\Pagination\DataAdapter\ModelCollection;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Pagination\DataAdapter\ModelCollection
 */
class ModelCollectionTest extends TestCase
{
    public function test()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['calcFoundRows', 'foundRows', 'limit'], [], '', false);
        $collection->expects($this->once())->method('calcFoundRows')->willReturnSelf();
        $collection->expects($this->once())->method('limit')->willReturnSelf();
        $collection->expects($this->once())->method('foundRows')->willReturn(4);
        $dataAdapter = new ModelCollection($collection);
        $dataAdapter->items(1,1);

        $this->assertSame(4, $dataAdapter->count());
    }
}