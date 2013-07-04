<?php

namespace Lazy\Db;

use Lazy\Db\Sql\Select;

/**
 * Class Collection
 * @package Lazy\Db
 */
class Collection implements \Countable, \Iterator
{
    /**
     * @var array
     */
    protected $fallbackMethods = array(
        'select'        => array('getSqlSelect', 'column'),
        'where'         => array('getSqlSelect', 'where'),
        'orWhere'       => array('getSqlSelect', 'where'),
        'having'        => array('getSqlSelect', 'having'),
        'orHaving'      => array('getSqlSelect', 'orHaving'),
        'join'          => array('getSqlSelect', 'join'),
        'leftJoin'      => array('getSqlSelect', 'leftJoin'),
        'rightJoin'     => array('getSqlSelect', 'rightJoin'),
        'order'         => array('getSqlSelect', 'order'),
        'group'         => array('getSqlSelect', 'group'),
        'limit'         => array('getSqlSelect', 'limit'),
        'offset'        => array('getSqlSelect', 'offset'),
    );
    /**
     * @var string
     */
    protected $modelClassName;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @var Sql\Select
     */
    protected $select;

    /**
     * @var array
     */
    protected $data;

    /**
     * @var callable
     */
    protected $callback;

    /**
     * @var array
     */
    protected $models = array();

    /**
     * @var int
     */
    protected $count;

    /**
     * @var int
     */
    protected $countAll;

    /**
     * @var int
     */
    protected $position = 0;

    /**
     * @param string $modelClassName
     * @param $data
     * @param callable $callback
     */
    public function __construct($modelClassName, Select $select, \Closure $callback = null)
    {
        $this->modelClassName = $modelClassName;
        $this->primaryKey = $modelClassName::getPrimaryKey();

        $this->select = $select;
        $this->callback = $callback;
    }

    /**
     * @param string $method
     * @param array $args
     * @return $this
     * @throws Exception
     */
    public function __call($method, $args)
    {
        if (!isset($this->fallbackMethods[$method])) {
            throw new Exception(sprintf('Call undefined method %s', $method));
        }

        $fallbackCallback = $this->fallbackMethods[$method];
        $fallbackCallback[0] = $this->{$fallbackCallback[0]}();
        call_user_func_array($fallbackCallback, $args);
        return $this;
    }

    /**
     * @return Select
     */
    public function getSqlSelect()
    {
        return $this->select;
    }

    /**
     * @param string|array $column
     * @return $this
     */
    public function select($column)
    {
        $select = $this->select;
        $select->resetColumn()->column($column);
        return $this;
    }

    /**
     * @return array
     */
    public function toArray()
    {
        if (null === $this->data) {
            $rows = $this->select->fetchAll(\PDO::FETCH_ASSOC);
            if ($this->callback) {
                $callback = $this->callback;
                $callback($rows);
            } else {
                $this->data = $rows;
            }
        }

        return $this->data;
    }

    /**
     * @param array $data
     * @return $this
     */
    public function setData(array $data)
    {
        $this->data = $data;
        return $this;
    }

    /**
     * @param int|string $columnKey
     * @return array
     */
    public function column($columnKey = 0)
    {
        $data = $this->toArray();
        if (is_numeric($columnKey)) {
            $data = array_map(function($item) {
                return array_merge($item, array_values($item));
            }, $data);
        }
        $result = array();
        foreach ($data as $row) {
            $result[] = $row[$columnKey];
        }
        return $result;
    }

    /**
     * @param int|string $columnKey
     * @param int|string $columnValue
     * @return array
     */
    public function pair($columnKey = 0, $columnValue = 1)
    {
        $data = $this->toArray();
        if (is_numeric($columnKey) || is_numeric($columnValue)) {
            $data = array_map(function($item) {
                return array_merge($item, array_values($item));
            }, $data);
        }

        $result = array();
        foreach ($data as $row) {
            $result[$row[$columnKey]] = $row[$columnValue];
        }
        return $result;
    }

    /**
     * @param int $id
     * @return null|AbstractModel
     */
    public function get($id)
    {
        if (isset($this->models[$id])) {
            return $this->models[$id];
        }

        foreach ($this->toArray() as $row) {
            if ($row[$this->primaryKey] == $id) {
                $modelClassName = $this->modelClassName;
                $model = new $modelClassName($row, $this);
                $this->models[$id] = $model;
                return $model;
            }
        }
    }

    public function delete()
    {
        foreach ($this as $model) {
            $model->delete();
        }

        return $this;
    }

    public function countAll()
    {
        if (null === $this->countAll) {
            $select = clone $this->select;
            $select->resetColumn()->resetOrder()->resetLimit()->column('COUNT(*)');
            $this->countAll = (int) $select->fetchColumn();
        }
        return $this->countAll;
    }

    /**
     * @return int
     */
    public function count()
    {
        if (null === $this->count) {
            if (is_array($this->data)) {
                $this->count = count($this->data);
            } else {
                if ($this->callback) {
                    $this->count = count($this->toArray());
                } else {
                    $select = clone $this->select;
                    $select->resetColumn()->resetOrder()->column('COUNT(*)');
                    $this->count = $select->fetchColumn();
                }
            }
        }

        return $this->count;
    }

    /**
     * @return AbstractModel
     */
    public function current()
    {
        $dataArray = $this->toArray();
        return $this->get($dataArray[$this->position][$this->primaryKey]);
    }

    /**
     *
     */
    public function next()
    {
        $this->position++;
    }

    /**
     * @return int
     */
    public function key()
    {
        return $this->position;
    }

    /**
     * @return bool
     */
    public function valid()
    {
        $dataArray = $this->toArray();
        return isset($dataArray[$this->position]);
    }

    /**
     *
     */
    public function rewind()
    {
        $this->position = 0;
    }
}