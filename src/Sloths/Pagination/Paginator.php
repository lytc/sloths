<?php

namespace Sloths\Pagination;

use Sloths\Pagination\Adapter\AdapterInterface;

class Paginator implements \IteratorAggregate
{
    /**
     * @var AdapterInterface
     */
    protected $adapter;

    /**
     * @var int
     */
    protected $itemCountPerPage = 50;

    /**
     * @var int
     */
    protected $pageRange = 10;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var int
     */
    protected $totalPages;

    /**
     * @param AdapterInterface $adapter
     * @param int $currentPage
     */
    public function __construct(AdapterInterface $adapter, $currentPage = 1)
    {
        $this->adapter = $adapter;
        $this->currentPage = $currentPage;
    }

    /**
     * @return AdapterInterface
     */
    public function getAdapter()
    {
        return $this->adapter;
    }

    /**
     * @param int $itemCountPerPage
     * @return $this
     */
    public function setItemCountPerPage($itemCountPerPage)
    {
        $this->itemCountPerPage = (int) $itemCountPerPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemCountPerPage()
    {
        return $this->itemCountPerPage;
    }

    /**
     * @param int $pageRange
     * @return $this
     */
    public function setPageRange($pageRange)
    {
        $this->pageRange = (int) $pageRange;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageRange()
    {
        return $this->pageRange;
    }

    /**
     * @param int $currentPage
     * @return $this
     */
    public function setCurrentPage($currentPage)
    {
        $this->currentPage = (int) $currentPage;
        return $this;
    }

    /**
     * @return int
     */
    public function getCurrentPage()
    {
        return max(1, min($this->currentPage, $this->getTotalPages()));
    }

    /**
     * @return int
     */
    public function getTotalItemCount()
    {
        return $this->getAdapter()->count();
    }

    public function isEmpty()
    {
        return $this->getTotalItemCount() == 0;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        if (null === $this->totalPages) {
            $this->totalPages = (int) ceil($this->getTotalItemCount() / $this->getItemCountPerPage());
        }

        return $this->totalPages;
    }

    /**
     * @return int
     */
    public function getFromIndex()
    {
        $fromIndex = ($this->getCurrentPage() - 1) * $this->getItemCountPerPage();
        return $this->getTotalItemCount()? max(0, $fromIndex) : 0;
    }

    /**
     * @return int
     */
    public function getToIndex()
    {
        return min($this->getFromIndex() + $this->getItemCountPerPage(), $this->getTotalItemCount());
    }

    /**
     * @return bool|int
     */
    public function getPrevPageNumber()
    {
        if (($prev = ($this->getCurrentPage() - 1)) > 0) {
            return $prev;
        }
        return false;
    }

    /**
     * @return bool|int
     */
    public function getNextPageNumber()
    {
        if (($next = ($this->getCurrentPage() + 1)) <= $this->getTotalPages()) {
            return $next;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getFirstPageInRange()
    {
        $result = max(1, $this->getCurrentPage() - (int) ceil($this->getPageRange() / 2));
        return max(1, min($result, $this->getTotalPages() - $this->getPageRange() + 1));
    }

    /**
     * @return int
     */
    public function getLastPageInRange()
    {
        return min($this->getTotalPages(), $this->getFirstPageInRange() + $this->getPageRange() - 1);
    }

    /**
     * @return \Traversable
     */
    public function getItems()
    {
        return $this->getAdapter()->getRange($this->getFromIndex(), $this->getItemCountPerPage());
    }

    /**
     * @return \Traversable
     */
    public function getIterator()
    {
        return $this->getItems();
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        $totalPage = $this->getTotalPages();
        $prev = $this->getPrevPageNumber();
        $next = $this->getNextPageNumber();

        return [
            'totalItem'             => $this->getTotalItemCount(),
            'firstPageInRange'      => $this->getFirstPageInRange(),
            'lastPageInRange'       => $this->getLastPageInRange(),
            'from'                  => $this->getFromIndex(),
            'to'                    => $this->getToIndex(),
            'totalPage'             => $totalPage,
            'currentPage'           => $this->getCurrentPage(),
            'itemCountPerPage'      => $this->getItemCountPerPage(),
            'first'                 => 1,
            'last'                  => $totalPage,
            'prev'                  => $prev > 0? $prev : null,
            'next'                  => $next <= $totalPage? $next : null,
        ];
    }
}