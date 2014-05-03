<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Db;

class Update implements SqlInterface
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var string
     */
    protected $set;

    /**
     * @var string
     */
    protected $where;

    /**
     * @var int
     */
    protected $limit;

    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * @param string [$table]
     */
    public function __construct($table = null)
    {
        !$table || $this->from($table);
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function ignore($state = true)
    {
        $this->ignore = $state;
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from($table)
    {
        $this->table = $table;
        return $this;
    }

    /**
     * @param array $values
     * @return $this
     */
    public function set(array $values)
    {
        $this->set = $values;
        return $this;
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
     * @param $limit
     * @return $this
     */
    public function limit($limit)
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $parts = ['UPDATE'];

        !$this->ignore || $parts[] = 'IGNORE';
        $parts[] = $this->table;

        #set part
        $set = [];
        foreach ($this->set as $key => $value) {
            $set[] = $key . ' = ' . Db::quote($value);
        }

        $parts[] = 'SET ' . implode(', ', $set);

        if ($this->where && ($wherePart = $this->where->toString())) {
            $parts[] = $wherePart;
        }

        !$this->limit || $parts[] = 'LIMIT ' . (int) $this->limit;

        return implode(' ', $parts);
    }
}