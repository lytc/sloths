<?php

namespace Sloths\Pagination\Adapter;

use Sloths\Db\Model\Collection;

class ModelCollection implements AdapterInterface
{
    /**
     * @var \Sloths\Db\Model\Collection
     */
    protected $collection;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return Collection
     */
    public function getModelCollection()
    {
        return $this->collection;
    }

    /**
     * @return int
     */
    public function count()
    {
        return $this->collection->foundRows();
    }

    /**
     * @param int $from
     * @param int $length
     * @return Collection
     */
    public function getRange($from, $length)
    {
        $this->collection->limit($length, $from);
        return $this->collection;
    }
}