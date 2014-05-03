<?php

namespace Lazy\Db\Model;

use Lazy\Db\Sql\Select;
use Lazy\Observer\ObserverTrait;
use Lazy\Util\ArrayUtils;

/**
 * @method distinct
 * @method calcFoundRows
 * @method select
 * @method where
 * @method orWhere
 * @method having
 * @method orHaving
 * @method orderBy
 * @method groupBy
 * @method limit
 * @method offset
 * @method join
 * @method leftJoin
 * @method rightJoin
 */
class Collection implements \Countable, \IteratorAggregate, \JsonSerializable, \ArrayAccess
{
    use ObserverTrait;

    /**
     * @var array
     */
    protected static $fallbackToSelectMethods = [
        'distinct', 'calcFoundRows', 'select', 'where', 'orWhere', 'having', 'orHaving',
        'orderBy', 'groupBy',
        'limit', 'offset',
        'join', 'leftJoin', 'rightJoin'
    ];

    /**
     * @var array
     */
    protected static $compositeMethods = ['save', 'delete'];

    /**
     * @var \Lazy\Db\Sql\Select
     */
    protected $sqlSelect;

    /**
     * @var Model
     */
    protected $modelClassName;

    /**
     * @var array
     */
    protected $models;

    /**
     * @var int
     */
    protected $foundRows;

    /**
     * @param Select $select
     * @param string $modelClassName
     */
    public function __construct(Select $select, $modelClassName)
    {
        $this->sqlSelect = $select;
        $this->modelClassName = $modelClassName;
    }

    /**
     * @param string $method
     * @param array $args
     * @return $this
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (in_array($method, static::$fallbackToSelectMethods)) {
            call_user_func_array([$this->sqlSelect, $method], $args);
            return $this;
        }

        if (in_array($method, static::$compositeMethods)) {
            foreach ($this as $model) {
                call_user_func_array([$model, $method], $args);
            }
            return $this;
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s', $method));
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        foreach ($this as $model) {
            $model->set($name, $value);
        }
    }

    /**
     * @return Select
     */
    public function getSqlSelect()
    {
        return $this->sqlSelect;
    }

    /**
     * @return \Lazy\Db\Connection
     */
    public function getConnection()
    {
        $modelClassName = $this->modelClassName;
        return $modelClassName::getConnection();
    }

    /**
     * @param array $rows
     */
    public function fromArray(array $rows)
    {
        $this->models = [];

        $modelClassName = $this->modelClassName;

        foreach ($rows as $row) {
            $this->models[] = new $modelClassName($row, $this);
        }
    }

    /**
     * @param bool [$reload=false]
     */
    protected function load($reload = false)
    {
        if ($reload || null === $this->models) {
            $connection = $this->getConnection();

            if ($this->sqlSelect->isCalcFoundRows()) {
                $result = $connection->selectAllWithFoundRows($this->sqlSelect);
                $this->foundRows = $result['foundRows'];
                $rows = $result['rows'];
            } else {
                $rows = $connection->selectAll($this->sqlSelect);
            }

            $this->notify('loaded', [&$rows]);

            $this->fromArray($rows);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        $this->load();
        return count($this->models);
    }

    /**
     * @return int
     */
    public function foundRows()
    {
        if (null === $this->foundRows) {
            if (null === $this->models) {
                $this->calcFoundRows();
                $this->load();
            } else {
                $sqlSelect = clone $this->sqlSelect;
                $sqlSelect->calcFoundRows()->limit(0);

                $result = $this->getConnection()->selectAllWithFoundRows($sqlSelect);
                $this->foundRows = $result['foundRows'];
            }
        }

        return $this->foundRows;
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        $this->load();
        return new \ArrayIterator($this->models);
    }

    /**
     * @param string $name
     * @param string [$columnKey=null]
     * @return array
     */
    public function column($name, $columnKey = null)
    {
        return ArrayUtils::column($this->toArray(), $name, $columnKey);
    }

    /**
     * @return array
     */
    public function ids()
    {
        $modelClassName = $this->modelClassName;
        return $this->column($modelClassName::getPrimaryKey());
    }

    /**
     * @return array
     */
    public function toArray()
    {
        $result = [];

        foreach ($this as $model) {
            $result[] = $model->toArray();
        }

        return $result;
    }

    /**
     * @param int $options
     * @return string
     */
    public function toJson($options = 0)
    {
        return json_encode($this, $options);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @param mixed $offset
     * @return bool
     */
    public function offsetExists($offset)
    {
        $this->load();
        return isset($this->models[$offset]);
    }

    /**
     * @param mixed $offset
     * @return null
     */
    public function offsetGet($offset)
    {
        return $this->offsetExists($offset)? $this->models[$offset] : null;
    }

    /**
     * @param mixed $offset
     * @param mixed $model
     * @throws \InvalidArgumentException
     */
    public function offsetSet($offset, $model)
    {
        $modelClassName = $this->modelClassName;

        if (!$model instanceof $modelClassName) {
            throw new \InvalidArgumentException(sprintf('Model class should be an instanceof %s', $modelClassName));
        }

        $this->models[] = $model;
    }

    /**
     * @param mixed $offset
     */
    public function offsetUnset($offset)
    {
        // TODO: Implement offsetUnset() method.
    }
}