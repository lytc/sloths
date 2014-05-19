<?php

namespace Sloths\Db\Sql;

use Sloths\Db\Db;

class Replace implements SqlInterface
{
    /**
     * @var string
     */
    protected $spec = 'REPLACE';

    /**
     * @var string
     */
    protected $table;

    /**
     * @var string|array
     */
    protected $columns;

    /**
     * @var array|Select
     */
    protected $values = [];

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @param string [$table]
     */
    public function __construct($table = null)
    {
        !$table || $this->into($table);
    }

    /**
     * @param string $option
     * @param bool $state
     * @return $this
     */
    protected function toggleOption($option, $state)
    {
        if ($state) {
            $this->options[$option] = $option;
        } else {
            unset($this->options[$option]);
        }

        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function into($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param string|array $columns
     * @return $this
     */
    public function columns($columns)
    {
        $this->columns = $columns;
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function values(array $values)
    {
        $this->values = $values;
        return $this;
    }

    /**
     * @param Select $select
     * @return $this
     */
    public function select(Select $select)
    {
        $this->values = $select;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $values = $this->values;

        if (is_array($values)) {
            is_array(current($values)) || ($values = [$values]);
        }

        $parts = [$this->spec];
        !$this->options || $parts[] = implode(' ', $this->options);
        $parts[] = 'INTO ' . $this->table;

        // columns part
        $columns = $this->columns;

        if (!$columns) {
            $columns = array_keys(current($values));
        }

        if (is_array($columns)) {
            $columns = implode(', ', $columns);
        }

        !$columns || $parts[] = '(' . $columns . ')';

        // values part
        if ($values instanceof Select) {
            $parts[] = $values->toString();
        } else {
            $valuePart = [];
            foreach ($values as &$value) {
                $value = Db::quote($value);
                $valuePart[] = '(' . implode(', ', $value) . ')';
            }
            $parts[] = 'VALUES ' . implode(', ', $valuePart);
        }

        return implode(' ', $parts);
    }
}