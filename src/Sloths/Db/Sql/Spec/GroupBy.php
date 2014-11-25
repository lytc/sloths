<?php

namespace Sloths\Db\Sql\Spec;

use Sloths\Db\Sql\SqlInterface;

class GroupBy implements SqlInterface
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @param string|array $items
     * @return $this
     */
    public function add($items)
    {
        if (!is_array($items)) {
            $items = [$items];
        }
        $this->items = array_merge($this->items, $items);
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->items = [];
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->items) {
            return '';
        }

        return 'GROUP BY ' . implode(', ', $this->items);
    }
}