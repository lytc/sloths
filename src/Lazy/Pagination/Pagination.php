<?php

namespace Lazy\Pagination;

use Lazy\Pagination\Exception\Exception;
use Lazy\Db\Sql\Select;
use Lazy\Db\Collection;
use Lazy\Pagination\Adapter\DbCollection;
use Lazy\Pagination\Adapter\AdapterInterface;

class Pagination implements \Countable, \IteratorAggregate
{
    protected $adapter;

    protected static $defaultItemCountPerPage = 20;
    protected $pageRange = 10;
    protected $itemCountPerPage;
    protected $totalPages;
    protected $currentPage = 1;
    protected $items;

    public function __construct($adapter, $itemCountPerPage = null)
    {
        if ($adapter instanceof Select) {
            $adapter = new DbSelect($adapter);
        } elseif ($adapter instanceof Collection) {
            $adapter = new DbCollection($adapter);
        }

        if (!$adapter instanceof AdapterInterface) {
            throw new Exception(sprintf('Pagination adapter must be an instanceof AdapterInterface. %s Given.', get_class($adapter)));
        }

        $this->adapter = $adapter;

        $this->itemCountPerPage = is_null($itemCountPerPage)?
            self::$defaultItemCountPerPage : (int) $itemCountPerPage;
    }

    public static function defaultItemCountPerPage($number = null)
    {
        if (!func_num_args()) {
            return self::$defaultItemCountPerPage;
        }

        self::$defaultItemCountPerPage = (int) $number;
    }

    public function itemCountPerPage($number = null)
    {
        if (!func_num_args()) {
            return $this->itemCountPerPage;
        }

        $this->itemCountPerPage = (int) $number;
        return $this;
    }

    public function totalPages()
    {
        if (null !== $this->totalPages) {
            return $this->totalPages;
        }

        $this->totalPages = ceil($this->adapter->count() / $this->itemCountPerPage);
        return $this->totalPages;
    }

    public function currentPage($currentPage = null)
    {
        if (!func_num_args()) {
            return $this->currentPage;
        }

        $this->currentPage = (int) $currentPage;
        $this->currentPage > 0 || $this->currentPage = 1;
        return $this;
    }

    public function offset()
    {
        return ($this->currentPage - 1) * $this->itemCountPerPage;
    }
    public function from()
    {
        return max(1, $this->offset() + 1);
    }

    public function to()
    {
        return min($this->from() + $this->itemCountPerPage - 1, $this->adapter->count());
    }

    public function items()
    {
        if (null !== $this->items) {
            return $this->items;
        }

        $this->items = $this->adapter->items($this->offset(), $this->itemCountPerPage);
        if (!$this->items instanceof \Traversable) {
            $this->items = new \ArrayIterator($this->items);
        }
        return $this->items;
    }

    public function info()
    {
        $totalPage = $this->totalPages();
        $currentPage = $this->currentPage();
        $prev = $currentPage - 1;
        $next = $currentPage + 1;
        $firstPageInRange = ceil($currentPage / $this->pageRange - 1) * $this->pageRange + 1;
        $lastPageInRange = $firstPageInRange + $this->pageRange - 1;

        if ($lastPageInRange > $totalPage) {
            $lastPageInRange = $totalPage;
        }

        return [
            'totalItem'             => $this->adapter->count(),
            'firstPageInRange'      => $firstPageInRange,
            'lastPageInRange'       => $lastPageInRange,
            'from'                  => $this->from(),
            'to'                    => $this->to(),
            'totalPage'             => $totalPage,
            'currentPage'           => $currentPage,
            'itemCountPerPage'      => $this->itemCountPerPage(),
            'first'                 => 1,
            'last'                  => $totalPage,
            'prev'                  => $prev > 0? $prev : null,
            'next'                  => $next <= $totalPage? $next : null,
        ];
    }

    public function prev()
    {
        if ($prev = ($this->currentPage - 1) > 0) {
            return $prev;
        }
    }

    public function next()
    {
        if (!$next = ($this->currentPage() + 1) <= $this->totalPages()) {
            return $next;
        }
    }

    public function count()
    {
        return $this->totalPages();
    }

    public function getIterator()
    {
        return $this->items();
    }
}