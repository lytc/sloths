<?php

namespace SlothsTest\Pagination\Adapter;

use SlothsTest\TestCase;

use Sloths\Pagination\Adapter\ArrayAdapter;

/**
 * @cover Sloths\Pagination\Adapter\ArrayAdapter
 */
class ArrayAdapterTest extends TestCase
{
    public function test()
    {
        $data = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        $adapter = new ArrayAdapter($data);

        $this->assertSame($data, $adapter->getData());

        $this->assertSame(count($data), $adapter->count());
        $this->assertSame([0, 1], $adapter->getRange(0, 2));
        $this->assertSame([2, 3, 4], $adapter->getRange(2, 3));
    }
}