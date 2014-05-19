<?php

namespace Sloths\Pagination\DataAdapter;

use Sloths\Db\Model\Collection;

class ModelCollection implements DataAdapterInterface
{
    /**
     * @var \Sloths\Db\Model\Collection
     */
    protected $collection;

    /**
     * @var int
     */
    protected $count;

    /**
     * @param Collection $collection
     */
    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    /**
     * @return mixed
     */
    public function count()
    {
        return $this->count;
    }

    /**
     * @param int $limit
     * @param int $offset
     * @return Collection
     */
    public function items($limit, $offset)
    {
        $this->collection->calcFoundRows()->limit($limit, $offset);
        $this->count = $this->collection->foundRows();

        return $this->collection;
    }
}
