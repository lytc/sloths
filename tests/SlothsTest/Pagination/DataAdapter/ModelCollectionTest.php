<?php

namespace SlothsTest\Pagination\DataAdapter;

use Sloths\Pagination\DataAdapter\ModelCollection;
use SlothsTest\TestCase;

class ModelCollectionTest extends TestCase
{
    public function test()
    {
        $collection = $this->mock('Sloths\Db\Model\Collection');
        $collection->shouldReceive('calcFoundRows')->once()->andReturnSelf();
        $collection->shouldReceive('limit')->once()->andReturnSelf();
        $collection->shouldReceive('foundRows')->once()->andReturn(4);
        $dataAdapter = new ModelCollection($collection);
        $dataAdapter->items(1,1);

        $this->assertSame(4, $dataAdapter->count());
    }
}