<?php

namespace Sloths\Db\Sql;

class Delete implements SqlInterface
{
    /**
     * @var string
     */
    protected $table;

    /**
     * @var
     */
    protected $where;

    /**
     * @var bool
     */
    protected $ignore = false;

    /**
     * @param string $table
     */
    public function __construct($table = null)
    {
        !$table || $this->from($table);
    }

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
     * @return string
     */
    public function toString()
    {
        $parts = ['DELETE'];
        !$this->ignore || $parts[] = 'IGNORE';
        $parts[] = 'FROM ' . $this->table;

        if ($this->where && ($wherePart = $this->where->toString())) {
            $parts[] = $wherePart;
        }

        return implode(' ', $parts);
    }
}