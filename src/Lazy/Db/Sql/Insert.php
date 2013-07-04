<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Connection;

/**
 * Class Insert
 * @package Lazy\Db\Sql
 */
class Insert
{
    /**
     * @var \Lazy\Db\Connection
     */
    protected $connection;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var array
     */
    protected $columns = array();
    /**
     * @var array
     */
    protected $values = array();

    /**
     * @param Connection $connection
     * @param null $table
     */
    public function __construct(Connection $connection, $table = null)
    {
        $this->connection = $connection;
        $this->into($table);
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function into($table = null)
    {
        if (!func_num_args()) {
            return $this->table;
        }

        $this->table = $table;
        return $this;
    }

    /**
     * @param string|array $columns
     * @return $this|array
     */
    public function column($columns = null)
    {
        if (!func_num_args()) {
            return $this->columns;
        }

        if (is_string($columns)) {
            $columns = preg_split('/\s*,\s+/', $columns);
        }

        $this->columns = $columns;
        return $this;
    }

    /**
     * @param array $values
     * @return $this|array
     */
    public function value(array $values = null)
    {
        if (!func_num_args()) {
            return $this->values;
        }

        $this->values = $values;
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->columns = array();
        $this->values = array();
        return $this;
    }

    /**
     * @return int
     */
    public function exec()
    {
        return $this->connection->exec($this->toString());
    }

    /**
     * @return string
     */
    public function toString()
    {
        $sql = array('INSERT INTO');

        # from
        $sql[] = $this->table;

        $values = $this->values;
        is_array(current($values)) || $values = array($values);

        # columns
        $columns = $this->columns?: array_keys(current($values));
        $sql[] = '(' . implode(', ', $columns) . ')';

        # set
        $vals = array();
        foreach ($values as $value) {
            $vals[] = '(' . implode(', ', $this->connection->quote(array_values($value))) . ')';
        }

        $sql[] = 'VALUES ' . implode(', ', $vals);

        return implode(' ', $sql);
    }
}