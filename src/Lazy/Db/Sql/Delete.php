<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Exception;
use Lazy\Db\Connection;

/**
 * Class Delete
 * @package Lazy\Db\Sql
 */
class Delete
{
    /**
     * @var Connection
     */
    protected $connection;
    /**
     * @var string
     */
    protected $table;
    /**
     * @var Where
     */
    protected $where;
    /**
     * @var Order
     */
    protected $order;
    /**
     * @var Limit
     */
    protected $limit;

    /**
     * @var array
     */
    protected static $aliasMethods = array(
        'orWhere'   => 'where',
    );

    /**
     * @param Connection $connection
     * @param string $table
     */
    public function __construct(Connection $connection, $table = null)
    {
        $this->connection = $connection;
        $this->from($table);
    }

    /**
     * @return Connection
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param string $method
     * @param array $args
     * @return $this
     * @throws Exception
     */
    public function __call($method, $args)
    {
        if (preg_match('/^reset(Where|Order|Limit)$/', $method)) {
            $part = strtolower(substr($method, 5));
            !$this->{$part} || $this->{$part}->reset();
            return $this;
        }

        if (!isset(self::$aliasMethods[$method])) {
            throw new Exception(sprintf('Call undefined method %s', $method));
        }

        call_user_func_array(array($this->{self::$aliasMethods[$method]}(), $method), $args);
        return $this;
    }

    /**
     * @param string $table
     * @return $this
     */
    public function from($table = null)
    {
        if (!func_num_args()) {
            return $this->table;
        }

        $this->table = $table;
        return $this;
    }

    /**
     * @param string|array|Where $where
     * @return $this|Where
     */
    public function where($where = null)
    {
        if ($where instanceof Where) {
            $this->where = $where;
            return $this;
        }

        if (!$this->where) {
            $this->where = new Where($this->connection);
        }

        if (!func_num_args()) {
            return $this->where;
        }

        call_user_func_array(array($this->where, 'where'), func_get_args());
        return $this;
    }

    /**
     * @param string|array|Order $order
     * @return $this|Order
     */
    public function order($order = null)
    {
        if ($order instanceof Order) {
            $this->order = $order;
            return $this;
        }

        if (!$this->order) {
            $this->order = new Order();
        }

        if (!func_num_args()) {
            return $this->order;
        }

        call_user_func_array(array($this->order, 'order'), func_get_args());
        return $this;
    }

    /**
     * @param int|Limit $limit
     * @return $this|Limit
     */
    public function limit($limit = null)
    {
        if ($limit instanceof Limit) {
            $this->limit = $limit;
            return $this;
        }

        if (!$this->limit) {
            $this->limit = new Limit();
        }

        if (!func_num_args()) {
            return $this->limit;
        }

        call_user_func_array(array($this->limit, 'limit'), func_get_args());
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        !$this->where || $this->where->reset();
        !$this->order || $this->order->reset();
        !$this->limit || $this->limit->reset();

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
        $sql = array('DELETE');

        # from
        $sql[] = 'FROM ' . $this->table;

        # where
        if ($this->where && $where = $this->where->toString()) {
            $sql[] = $where;
        }

        $parts = array('order', 'limit');
        foreach ($parts as $part) {
            if ($this->{$part} && $part = $this->{$part}->toString()) {
                $sql[] = $part;
            }
        }

        return implode(' ', $sql);
    }
}