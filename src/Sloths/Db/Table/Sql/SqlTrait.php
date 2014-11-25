<?php

namespace Sloths\Db\Table\Sql;

use Sloths\Db\Connection;

trait SqlTrait
{
    /**
     * @var Connection
     */
    protected $connection;

    /**
     * @param Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @param bool $strict
     * @return Connection
     * @throws \RuntimeException
     */
    public function getConnection($strict = true)
    {
        if (!$this->connection && $strict) {
            throw new \RuntimeException('A connection is required');
        }

        return $this->connection;
    }
}