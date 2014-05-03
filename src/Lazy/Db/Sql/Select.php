<?php

namespace Lazy\Db\Sql;

use Lazy\Util\Inflector;

class Select implements SqlInterface
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $tableAlias;

    /**
     * @var string|array
     */
    protected $columns = [];

    /**
     * @var Where
     */
    protected $where;

    /**
     * @var string|array
     */
    protected $orderBy;

    /**
     * @var string|array
     */
    protected $groupBy;

    /**
     * @var Having
     */
    protected $having;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var int
     */
    protected $offset;

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @var bool
     */
    protected $distinct = false;

    /**
     * @var bool
     */
    protected $calcFoundRows = false;

    /**
     * @param string $table
     */
    public function __construct($table = null)
    {
        !$table || $this->from($table);
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function distinct($state = true)
    {
        $this->distinct = $state;
        return $this;
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function calcFoundRows($state = true)
    {
        $this->calcFoundRows = $state;
        return $this;
    }

    /**
     * @return bool
     */
    public function isCalcFoundRows()
    {
        return $this->calcFoundRows;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $parts = preg_split('/(\s+as\s+|\s+)/', $table);
        $this->table = $parts[0];
        empty($parts[1]) || $this->tableAlias = $parts[1];
        return $this;
    }

    /**
     * @param string $prefix
     * @param string|array $columns
     * @return $this
     */
    public function select($prefix, $columns = null)
    {
        if (!$columns) {
            $columns = $prefix;
            $prefix = '';
        }
        $this->columns[$prefix] = $columns;
        return $this;
    }

    /**
     * @param string|array $orderBy
     */
    public function orderBy($orderBy)
    {
        $this->orderBy = $orderBy;
    }

    /**
     * @return $this|Where
     */
    public function where()
    {
        $this->where || $this->where = new Where();

        if (0 == func_num_args()) {
            return $this->where;
        }

        call_user_func_array([$this->where, 'where'], func_get_args());
        return $this;
    }

    /**
     * @return $this|Where
     */
    public function orWhere()
    {
        $this->where || $this->where = new Where();

        if (0 == func_num_args()) {
            return $this->where;
        }

        call_user_func_array([$this->where, 'orWhere'], func_get_args());
        return $this;
    }

    /**
     * @return $this|Having
     */
    public function having()
    {
        $this->having || $this->having = new Having();

        if (0 == func_num_args()) {
            return $this->having;
        }

        call_user_func_array([$this->having, 'having'], func_get_args());
        return $this;
    }

    /**
     * @return $this|Having
     */
    public function orHaving()
    {
        $this->having || $this->having = new Having();

        if (0 == func_num_args()) {
            return $this->having;
        }

        call_user_func_array([$this->having, 'orHaving'], func_get_args());
        return $this;
    }

    /**
     * @param string|array $groupBy
     * @return $this
     */
    public function groupBy($groupBy)
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @param int $limit
     * @param int [$offset]
     * @return $this
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;
        if (is_numeric($offset)) {
            $this->offset = $offset;
        }

        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @param string $type
     * @param string $table
     * @param string [$condition]
     * @return $this
     */
    protected function _join($type, $table, $condition = null)
    {
        $this->joins[] = [
            'type' => $type,
            'table' => $table,
            'condition' => $condition
        ];

        return $this;
    }

    /**
     * @param string $table
     * @param string [$condition]
     * @return $this
     */
    public function join($table, $condition = null)
    {
        return $this->_join('INNER', $table, $condition);
    }

    /**
     * @param string $table
     * @param string [$condition]
     * @return $this
     */
    public function leftJoin($table, $condition = null)
    {
        return $this->_join('LEFT', $table, $condition);
    }

    /**
     * @param string $table
     * @param string [$condition]
     * @return $this
     */
    public function rightJoin($table, $condition = null)
    {
        return $this->_join('RIGHT', $table, $condition);
    }

    /**
     * @return string
     */
    protected function processColumn()
    {
        if (!$this->columns) {
            return ($this->tableAlias?: $this->table) . '.*';
        }

        $parts = [];
        foreach ($this->columns as $prefix => $columns) {
            if (is_array($columns)) {
                foreach ($columns as $alias => $name) {
                    if ($name instanceof Select) {
                        $name = '(' . $name->toString() . ')';
                    } elseif ($prefix) {
                        $name = $prefix . '.' . $name;
                    }

                    if (!is_numeric($alias)) {
                        $name = $name . ' ' . $alias;
                    }

                    $parts[] = $name;
                }
            } else {
                $parts[] = $columns;
            }

        }


        return implode(', ', $parts);
    }

    /**
     * @return null|string
     */
    protected function processOrderBy()
    {
        if ($this->orderBy) {
            $result = 'ORDER BY ';
            if (is_array($this->orderBy)) {
                $result .= implode(', ', $this->orderBy);
            } else {
                $result .= $this->orderBy;
            }
            return $result;
        }
    }

    /**
     * @return null|string
     */
    protected function processGroupBy()
    {
        if ($this->groupBy) {
            $result = 'GROUP BY ';
            if (is_array($this->groupBy)) {
                $result .= implode(', ', $this->groupBy);
            } else {
                $result .= $this->groupBy;
            }
            return $result;
        }
    }

    /**
     * @return string
     */
    protected function processLimit()
    {
        if ($this->limit === null) {
            return;
        }

        $limit = (int) $this->limit;
        $result = 'LIMIT ' . $limit;

        if ($this->offset !== null) {
            $result .= ' OFFSET ' . (int) $this->offset;
        }

        return $result;
    }

    protected function processJoin()
    {
        if (!$this->joins) {
            return;
        }

        $spec = '%s JOIN %s ON %s';

        $joins = [];
        foreach ($this->joins as $join) {
            $table = trim($join['table']);
            $tableParts = preg_split('/\s+/', $table);
            $name = isset($tableParts[1])? $tableParts[1] : $tableParts[0];

            $condition = $join['condition'];
            if (!$condition) {
                $condition = $name . '.' . Inflector::singularize($this->table) . '_id';
                $condition .= ' = ' . ($this->tableAlias?: $this->table) . '.id';
            }
            $joins[] = sprintf($spec, $join['type'], $join['table'], $condition);
        }

        return implode(' ', $joins);
    }

    /**
     * @return string
     */
    public function toString()
    {
        $parts = ['SELECT'];

        if ($this->distinct) {
            $parts[] = 'DISTINCT';
        }

        if ($this->calcFoundRows) {
            $parts[] = 'SQL_CALC_FOUND_ROWS';
        }

        $parts[] = $this->processColumn();
        $parts[] = 'FROM ' . $this->table . ($this->tableAlias? ' ' . $this->tableAlias : '');

        if ($joinPart = $this->processJoin()) {
            $parts[] = $joinPart;
        }

        if ($this->where && ($wherePart = $this->where->toString())) {
            $parts[] = $wherePart;
        }

        if ($groupPart = $this->processGroupBy()) {
            $parts[] = $groupPart;
        }

        if ($this->having && ($havingPart = $this->having->toString())) {
            $parts[] = $havingPart;
        }

        if ($orderByPart = $this->processOrderBy()) {
            $parts[] = $orderByPart;
        }

        if ($limitPart = $this->processLimit()) {
            $parts[] = $limitPart;
        }

        return implode(' ', $parts);
    }
}