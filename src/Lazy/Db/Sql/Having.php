<?php

namespace Lazy\Db\Sql;

/**
 * Class Having
 * @package Lazy\Db\Sql
 */
class Having extends AbstractCondition
{
    /**
     * @var string
     */
    protected $type = 'HAVING';

    /**
     * @param string|array $conditions
     * @param mixed|array|args $bindPrams
     * @return $this
     */
    public function having($conditions, $bindPrams = null)
    {
        if (func_num_args() > 2) {
            $bindPrams = array_slice(func_get_args(), 1);
        }

        is_array($bindPrams) || $bindPrams = array($bindPrams);
        return $this->condition('AND', $conditions, $bindPrams);
    }

    /**
     * @param string|array $conditions
     * @param mixed|array|args $bindPrams
     * @return $this
     */
    public function orHaving($conditions,  $bindPrams = null)
    {
        if (func_num_args() > 2) {
            $bindPrams = array_slice(func_get_args(), 1);
        }

        is_array($bindPrams) || $bindPrams = array($bindPrams);
        return $this->condition('OR', $conditions, $bindPrams);
    }
}