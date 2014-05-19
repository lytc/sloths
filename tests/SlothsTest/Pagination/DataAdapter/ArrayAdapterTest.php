<?php

namespace SlothsTest\Pagination\DataAdapter;

use Sloths\Pagination\DataAdapter\ArrayAdapter;

class ArrayAdapterTest extends \PHPUnit_Framework_TestCase
{
    public function test()
    {
        $data = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
        $dataAdapter = new ArrayAdapter($data);

        $this->assertCount(2, $dataAdapter->items(2, 3));
        $this->assertSame([4, 5], $dataAdapter->items(2, 3));
        $this->assertSame(10, $dataAdapter->count());
    }
}