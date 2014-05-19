<?php

namespace Sloths\Pagination;

use Sloths\Db\Model\Collection;
use Sloths\Db\Sql\Select;
use Sloths\Pagination\DataAdapter\ArrayAdapter;
use Sloths\Pagination\DataAdapter\DataAdapterInterface;
use Sloths\Pagination\DataAdapter\DbSelect;
use Sloths\Pagination\DataAdapter\ModelCollection;

class Paginator implements \Countable, \IteratorAggregate
{
    /**
     * @var int
     */
    protected static $defaultItemsCountPerPage = 50;

    /**
     * @var int
     */
    protected static $defaultPageRange = 10;

    /**
     * @var DataAdapter\DataAdapterInterface
     */
    protected $dataAdapter;

    /**
     * @var int
     */
    protected $itemsCountPerPage;

    /**
     * @var int
     */
    protected $pageRange;

    /**
     * @var int
     */
    protected $totalItemsCount;

    /**
     * @var int
     */
    protected $totalPages;

    /**
     * @var int
     */
    protected $currentPage = 1;

    /**
     * @var \Traversable
     */
    protected $items;

    /**
     * @param int $number
     */
    public static function setDefaultItemsCountPerPage($number)
    {
        static::$defaultItemsCountPerPage = $number;
    }

    /**
     * @return int
     */
    public static function getDefaultItemsCountPerPage()
    {
        return static::$defaultItemsCountPerPage;
    }

    /**
     * @param int $number
     */
    public static function setDefaultPageRange($number)
    {
        static::$defaultPageRange = $number;
    }

    /**
     * @return int
     */
    public static function getDefaultPageRange()
    {
        return static::$defaultPageRange;
    }

    /**
     * @param DataAdapterInterface|array|Collection|Select $dataAdapter
     * @throws \InvalidArgumentException
     */
    public function __construct($dataAdapter)
    {
        if ($dataAdapter instanceof Collection) {
            $dataAdapter = new ModelCollection($dataAdapter);
        } elseif ($dataAdapter instanceof Select) {
            $dataAdapter = new DbSelect($dataAdapter, func_get_arg(1));
        } else if (is_array($dataAdapter)) {
            $dataAdapter = new ArrayAdapter($dataAdapter);
        }

        if (!$dataAdapter instanceof DataAdapterInterface) {
            throw new \InvalidArgumentException(sprintf('Pagination adapter must be an instanceof AdapterInterface. %s Given.', get_class($dataAdapter)));
        }

        $this->dataAdapter = $dataAdapter;
    }

    /**
     * @return DataAdapterInterface
     */
    public function getDataAdapter()
    {
        return $this->dataAdapter;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function setItemsCountPerPage($number)
    {
        $this->itemsCountPerPage = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getItemsCountPerPage()
    {
        return $this->itemsCountPerPage?: static::$defaultItemsCountPerPage;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function setPageRange($number)
    {
        $this->pageRange = $number;
        return $this;
    }

    /**
     * @return int
     */
    public function getPageRange()
    {
        return $this->pageRange?: static::$defaultPageRange;
    }

    /**
     * @return int
     */
    public function getTotalItemsCount()
    {
        if (null === $this->totalItemsCount) {
            $this->getItems();
            $this->totalItemsCount = $this->dataAdapter->count();
        }

        return $this->totalItemsCount;
    }

    /**
     * @return int
     */
    public function getTotalPages()
    {
        if (null === $this->totalPages) {
            $this->totalPages = (int) ceil($this->getTotalItemsCount() / $this->getItemsCountPerPage());
        }

        return $this->totalPages;
    }

    /**
     * @param int $number
     * @return $this
     */
    public function setCurrentPage($number)
    {
        $number = (int) $number;
        $number > 0 || $number = 1;

        $this->currentPage = $number;
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
    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->getItemsCountPerPage();
    }

    /**
     * @return int
     */
    public function getFromIndex()
    {
        return $this->getTotalItemsCount()? max(1, $this->getOffset() + 1) : 0;
    }

    /**
     * @return int
     */
    public function getToIndex()
    {
        return min($this->getFromIndex() + $this->getItemsCountPerPage() - 1, $this->getTotalItemsCount());
    }

    /**
     * @return bool|int
     */
    public function getPrevPageNumber()
    {
        if (($prev = ($this->currentPage - 1)) > 0) {
            return $prev;
        }
        return false;
    }

    /**
     * @return bool|int
     */
    public function getNextPageNumber()
    {
        if (($next = ($this->currentPage + 1)) <= $this->getTotalPages()) {
            return $next;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getFirstPageInRange()
    {
        $result = max(1, $this->currentPage - (int) ceil($this->getPageRange() / 2));
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
     * @return \ArrayIterator|\Traversable
     */
    public function getItems()
    {
        if (null === $this->items) {
            $this->items = $this->dataAdapter->items($this->getItemsCountPerPage(), $this->getOffset());

            if (!$this->items instanceof \Traversable) {
                $this->items = new \ArrayIterator($this->items);
            }
        }

        return $this->items;
    }

    /**
     * @return array
     */
    public function getInfo()
    {
        return [
            'pages'             => $this->getTotalPages(),
            'currentPage'       => $this->getCurrentPage(),
            'itemsCountPerPage' => $this->getItemsCountPerPage(),
            'fromIndex'         => $this->getFromIndex(),
            'toIndex'           => $this->getToIndex(),
            'prev'              => $this->getPrevPageNumber(),
            'next'              => $this->getNextPageNumber(),
            'firstPageInRange'  => $this->getFirstPageInRange(),
            'lastPageInRange'   => $this->getLastPageInRange()
        ];
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->getTotalPages();
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return $this->getItems();
    }
}