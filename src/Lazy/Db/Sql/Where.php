<?php

namespace Lazy\Db\Sql;

/**
 * Class Where
 * @package Lazy\Db\Sql
 */
class Where extends AbstractCondition
{
    /**
     * @var string
     */
    protected $type = 'WHERE';

    /**
     * @param string|array $conditions
     * @param mixed|array|args $bindPrams
     * @return $this
     */
    public function where($conditions = null, $bindPrams = null)
    {
        if (!$conditions) {
            return $this->conditions;
        }

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
    public function orWhere($conditions,  $bindPrams = null)
    {
        if (func_num_args() > 2) {
            $bindPrams = array_slice(func_get_args(), 1);
        }

        is_array($bindPrams) || $bindPrams = array($bindPrams);
        return $this->condition('OR', $conditions, $bindPrams);
    }
}