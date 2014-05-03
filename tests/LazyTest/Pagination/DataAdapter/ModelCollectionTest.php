<?php

namespace LazyTest\Pagination\DataAdapter;

use Lazy\Pagination\DataAdapter\ModelCollection;
use LazyTest\TestCase;

class ModelCollectionTest extends TestCase
{
    public function test()
    {
        $collection = $this->mock('Lazy\Db\Model\Collection');
        $collection->shouldReceive('calcFoundRows')->once()->andReturnSelf();
        $collection->shouldReceive('limit')->once()->andReturnSelf();
        $collection->shouldReceive('foundRows')->once()->andReturn(4);
        $dataAdapter = new ModelCollection($collection);
        $dataAdapter->items(1,1);

        $this->assertSame(4, $dataAdapter->count());
    }
}