<?php

namespace Lazy\Pagination\Adapter;

use Lazy\Db\Sql\Select;

class DbSelect implements AdapterInterface
{
    protected $select;
    protected $count;

    public function __construct(Select $select)
    {
        $this->select = $select;
    }

    public function count()
    {
        if ($this->count !== null) {
            return $this->count;
        }

        $select = clone $this->select;
        $select->resetColumn();
        $select->limit()->reset();
        $select->order()->reset();
        $this->count = $select->column('COUNT(*)')->fetchColumn();

        return $this->count;
    }

    public function items($offset, $limit)
    {
        $offset             = (int) $offset;
        $limit              = (int) $limit;
        $currentOffset      = $this->select->offset();

        is_null($currentOffset) || $offset += $currentOffset;
        $select = clone $this->select;
        $select->limit($limit)->offset($offset);

        return $select->all();
    }
}