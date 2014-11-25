<?php

namespace Sloths\Db;

use Sloths\Cache\CacheableTrait;
use Sloths\Db\Table\Sql\Delete;
use Sloths\Db\Table\Sql\Insert;
use Sloths\Db\Table\Sql\Select;
use Sloths\Db\Table\Sql\Update;

class Table
{
    use CacheableTrait;

    /**
     * @var string
     */
    protected $name;

    /**
     * @var string
     */
    protected $primaryKey;

    /**
     * @var ConnectionManager
     */
    protected $connectionManager;

    /**
     * @param string $name
     * @param string $primaryKey;
     */
    public function __construct($name, $primaryKey = 'id')
    {
        $this->name = $name;
        $this->primaryKey = $primaryKey;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getPrimaryKey()
    {
        return $this->primaryKey;
    }

    /**
     * @param ConnectionManager $connectionManager
     * @return $this
     */
    public function setConnectionManager(ConnectionManager $connectionManager)
    {
        $this->connectionManager = $connectionManager;

        if (!$this->cacheManager && $cacheManager = $connectionManager->getCacheManager(false)) {
            $this->setCacheManager($cacheManager);
        }

        return $this;
    }

    /**
     * @param bool $strict
     * @return ConnectionManager
     * @throws \RuntimeException
     */
    public function getConnectionManager($strict = true)
    {
        if (!$this->connectionManager && $strict) {
            throw new \RuntimeException('A connection manager is required');
        }

        return $this->connectionManager;
    }

    /**
     * @param null $columns
     * @return Select
     */
    public function select($columns = null)
    {
        $select = new Select();
        $select->setConnection($this->getConnectionManager()->getReadConnection())->table($this->getName());

        if ($this->cacheManager) {
            $select->setCacheManager($this->cacheManager);
        }

        if ($columns) {
            $select->select($columns);
        }

        return $select;
    }

    /**
     * @param int $id
     * @param null $columns
     * @return Sql\Select
     */
    public function selectById($id, $columns = null)
    {
        return $this->select($columns)->where($this->getName() . '.' . $this->getPrimaryKey() . ' = ' . (int) $id);
    }

    /**
     * @param array $values
     * @return Insert
     */
    public function insert(array $values = null)
    {
        $insert = new Insert();
        $insert->setConnection($this->getConnectionManager()->getWriteConnection())->table($this->getName());

        if ($values) {
            $insert->values($values);
        }

        return $insert;
    }

    /**
     * @param array $values
     * @return Update
     */
    public function update(array $values = null)
    {
        $update = new Update();
        $update->setConnection($this->getConnectionManager()->getWriteConnection())->table($this->getName());

        if ($values) {
            $update->values($values);
        }

        return $update;
    }

    /**
     * @param null $where
     * @return Delete
     */
    public function delete($where = null, $params = null)
    {
        $delete = new Delete();
        $delete->setConnection($this->getConnectionManager()->getWriteConnection())->table($this->getName());

        if ($where) {
            $delete->where($where, $params);
        }

        return $delete;
    }
}