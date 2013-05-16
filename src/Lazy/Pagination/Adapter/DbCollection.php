<?php

namespace Lazy\Pagination\Adapter;

use Lazy\Db\Collection;

class DbCollection implements AdapterInterface
{
    protected $count;
    protected $collection;

    public function __construct(Collection $collection)
    {
        $this->collection = $collection;
    }

    public function count()
    {
        if ($this->count !== null) {
            return $this->count;
        }

        $this->count = $this->collection->countAll();
        return $this->count;
    }

    public function items($offset, $limit)
    {
        $select = $this->collection->getSqlSelect();

        $offset             = (int) $offset;
        $limit              = (int) $limit;
        $currentOffset      = $select->offset()->offset();

        is_null($currentOffset) || $offset += $currentOffset;
        $select->limit($limit)->offset($offset);

        return $this->collection;
    }
}