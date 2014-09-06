<?php

namespace Sloths\Db\Sql\Spec;

use Sloths\Db\Database;
use Sloths\Db\Sql\SqlInterface;

class Filter implements SqlInterface
{
    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $filters = [];

    public function __call($method, $args)
    {
        switch ($method) {
            case 'add':
            case 'and':
                array_unshift($args, 'AND');
                return call_user_func_array([$this, 'addFilter'], $args);

            case 'or':
                array_unshift($args, 'OR');
                return call_user_func_array([$this, 'addFilter'], $args);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s', get_called_class(), $method));
    }

    /**
     * @param string $type
     * @param string|array|callable $condition
     * @param string|array $params
     * @return $this
     */
    protected function addFilter($type, $condition, $params = null)
    {
        $filter = [$type];

        if (is_string($condition) && func_num_args() == 3) {
            $filter[] = [$condition => $params];
        } else {
            $filter[] = $condition;
        }

        $this->filters[] = $filter;
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->filters = [];
        return $this;
    }

    /**
     * @param $filter
     * @return array
     */
    protected function compileFilter($filter)
    {
        list($type, $condition) = $filter;

        if (is_string($condition) || $filter instanceof Raw) {
            return [$type, $condition];
        } elseif (is_array($condition)) {
            $result = [];
            foreach ($condition as $k => $v) {
                if (is_numeric($k)) {
                    $result[] = $v;
                } else {
                    $result[] = Database::bind($k, $v);
                }
            }

            return [$type, implode(' AND ', $result)];
        }

        $subFilter = new static();
        $subFilter->type = '';

        call_user_func($condition, $subFilter);
        return [$type, $subFilter->toString()];
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->filters) {
            return '';
        }

        $filters = $this->filters;
        $result = [];

        list($type, $condition) = $this->compileFilter(array_shift($filters));
        $result[] = '(' . $condition . ')';

        foreach ($filters as $filter) {
            list($type, $condition) = $this->compileFilter($filter);
            $result[] = $type . ' (' . $condition . ')';
        }

        return ($this->type? $this->type . ' ' : '') . implode(' ', $result);
    }
}