<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Connection;

/**
 * Class Order
 * @package Lazy\Db\Sql
 */
class Order
{
    /**
     * @var array
     */
    protected $orders = array();

    /**
     * @param string|array|args $order
     */
    public function __construct($order = null)
    {
        if ($order) {
            call_user_func_array(array($this, 'order'), func_get_args());
        }
    }

    /**
     * @param string|array|args $order optional
     * @return $this|array
     */
    public function order($order = null)
    {
        if (!$order) {
            return $this->orders;
        }

        $orders = func_get_args();
        foreach ($orders as $order) {
            if (is_string($order)) {
                $order = preg_split('/\s*,\s*/', trim($order));
            }

            foreach ($order as $column => $direction) {
                if (is_numeric($column)) {
                    $parts = preg_split('/\s+/', $direction);
                    $column = $parts[0];
                    if (isset($parts[1])) {
                        $direction = $parts[1];
                    } else {
                        $direction = 'ASC';
                    }
                }

                $this->orders[$column] = $direction;
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->orders = array();
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->orders) {
            return '';
        }

        $parts = array();
        foreach ($this->orders as $column => $direction) {
            $parts[] = $column . ' ' . $direction;
        }

        return 'ORDER BY ' . implode(', ', $parts);
    }
}