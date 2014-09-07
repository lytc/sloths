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
     * @var
     */
    protected $name;
    /**
     * @var
     */
    protected $database;

    /**
     * @param $name
     */
    public function __construct($name)
    {
        $this->name = $name;
    }

    /**
     * @param Database $database
     * @return $this
     */
    public function setDatabase(Database $database)
    {
        $this->database = $database;
        return $this;
    }

    /**
     * @param bool $strict
     * @return Database
     * @throws \RuntimeException
     */
    public function getDatabase($strict = true)
    {
        if (!$this->database && $strict) {
            throw new \RuntimeException('A database is required');
        }

        return $this->database;
    }

    /**
     * @param null $columns
     * @return Select
     */
    public function select($columns = null)
    {
        $select = new Select();
        $select->setConnection($this->getDatabase()->getReadConnection())->table($this->name);

        if ($this->cacheManager) {
            $select->setCacheManager($this->cacheManager);
        }

        if ($columns) {
            $select->select($columns);
        }

        return $select;
    }

    /**
     * @param array $values
     * @return Insert
     */
    public function insert(array $values = null)
    {
        $insert = new Insert();
        $insert->setConnection($this->getDatabase()->getWriteConnection())->table($this->name);

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
        $update->setConnection($this->getDatabase()->getWriteConnection())->table($this->name);

        if ($values) {
            $update->values($values);
        }

        return $update;
    }

    /**
     * @param null $where
     * @return Delete
     */
    public function delete($where = null)
    {
        $delete = new Delete();
        $delete->setConnection($this->getDatabase()->getWriteConnection())->table($this->name);

        if ($where) {
            $delete->where($where);
        }

        return $delete;
    }
}