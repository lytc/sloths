<?php

namespace Sloths\Db\Sql\Spec;

use Sloths\Db\Sql\SqlInterface;

class OrderBy implements SqlInterface
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

        $items = [];
        foreach ($this->items as $item => $direction) {
            $items[] = is_numeric($item)? $direction : $item . ' ' . $direction;
        }

        return 'ORDER BY ' . implode(', ', $items);
    }
}