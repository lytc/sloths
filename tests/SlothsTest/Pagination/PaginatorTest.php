<?php

namespace SlothsTest\Pagination;

use Sloths\Pagination\Adapter\ArrayAdapter;
use SlothsTest\TestCase;
use Sloths\Pagination\Paginator;

/**
 * @covers Sloths\Pagination\Paginator
 */
class PaginatorTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($currentPage, $totalItems, $itemCountPerPage, $pageRange, $expected)
    {
        $adapter = $this->getMock('Sloths\Pagination\Adapter\AdapterInterface');
        $adapter->expects($this->any())->method('count')->willReturn($totalItems);

        $paginator = new Paginator($adapter);
        $paginator
            ->setCurrentPage($currentPage)
            ->setItemCountPerPage($itemCountPerPage)
            ->setPageRange($pageRange);

        $this->assertSame($expected, [
            'getTotalItemCount' => $paginator->getTotalItemCount(),
            'currentPage' => $paginator->getCurrentPage(),
            'getFromIndex' => $paginator->getFromIndex(),
            'getToIndex' => $paginator->getToIndex(),
            'getPrevPageNumber' => $paginator->getPrevPageNumber(),
            'getNextPageNumber' => $paginator->getNextPageNumber(),
            'getFirstPageInRange' => $paginator->getFirstPageInRange(),
            'getLastPageInRange' => $paginator->getLastPageInRange()
        ]);
    }

    public function dataProvider()
    {
        return [
            [
                1, // current page
                100, // total item count
                10, // item count per page
                10, // page range
                [
                    'getTotalItemCount' => 100,
                    'currentPage' => 1,
                    'getFromIndex' => 0,
                    'getToIndex' => 10,
                    'getPrevPageNumber' => false,
                    'getNextPageNumber' => 2,
                    'getFirstPageInRange' => 1,
                    'getLastPageInRange' => 10
                ]
            ],
            [
                6, // current page
                100, // total item count
                10, // item count per page
                10, // page range
                [
                    'getTotalItemCount' => 100,
                    'currentPage' => 6,
                    'getFromIndex' => 50,
                    'getToIndex' => 60,
                    'getPrevPageNumber' => 5,
                    'getNextPageNumber' => 7,
                    'getFirstPageInRange' => 1,
                    'getLastPageInRange' => 10
                ]
            ],
            [
                7, // current page
                200, // total item count
                10, // item count per page
                10, // page range
                [
                    'getTotalItemCount' => 200,
                    'currentPage' => 7,
                    'getFromIndex' => 60,
                    'getToIndex' => 70,
                    'getPrevPageNumber' => 6,
                    'getNextPageNumber' => 8,
                    'getFirstPageInRange' => 2,
                    'getLastPageInRange' => 11
                ]
            ],
            [
                13, // current page
                300, // total item count
                10, // item count per page
                10, // page range
                [
                    'getTotalItemCount' => 300,
                    'currentPage' => 13,
                    'getFromIndex' => 120,
                    'getToIndex' => 130,
                    'getPrevPageNumber' => 12,
                    'getNextPageNumber' => 14,
                    'getFirstPageInRange' => 8,
                    'getLastPageInRange' => 17
                ]
            ],
            [
                11, // current page
                100, // total item count
                10, // item count per page
                10, // page range
                [
                    'getTotalItemCount' => 100,
                    'currentPage' => 10,
                    'getFromIndex' => 90,
                    'getToIndex' => 100,
                    'getPrevPageNumber' => 9,
                    'getNextPageNumber' => false,
                    'getFirstPageInRange' => 1,
                    'getLastPageInRange' => 10
                ]
            ]
        ];
    }

    public function testIsEmpty()
    {
        $paginator = $this->getMock('Sloths\Pagination\Paginator', ['getTotalItemCount'], [], '', false);
        $paginator->expects($this->once())->method('getTotalItemCount')->willReturn(0);
        $this->assertTrue($paginator->isEmpty());
    }

    public function testGetItems()
    {
        $adapter = $this->getMock('Sloths\Pagination\Adapter\AdapterInterface');
        $adapter->expects($this->once())->method('getRange')->with(0, 10)->willReturn('foo');

        $paginator = new Paginator($adapter);
        $paginator->setItemCountPerPage(10);
        $this->assertSame('foo', $paginator->getItems());
    }

    public function testGetIterator()
    {
        $paginator = $this->getMock('Sloths\Pagination\Paginator', ['getItems'], [], '', false);
        $paginator->expects($this->once())->method('getItems')->willReturn('foo');

        $this->assertSame('foo', $paginator->getIterator());
    }

    public function testGetInfo()
    {
        $adapter = new ArrayAdapter(range(1, 100000));
        $paginator = new Paginator($adapter);

        $expected = [
            'totalItem'             => 100000,
            'firstPageInRange'      => 1,
            'lastPageInRange'       => 10,
            'from'                  => 0,
            'to'                    => 50,
            'totalPage'             => 2000,
            'currentPage'           => 1,
            'itemCountPerPage'      => 50,
            'first'                 => 1,
            'last'                  => 2000,
            'prev'                  => null,
            'next'                  => 2
        ];

        $this->assertSame($expected, $paginator->getInfo());
    }
}