<?php

namespace Sloths\Db\Migration;

use Sloths\Db\Connection;

abstract class AbstractMigration implements MigrationInterface
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
     * @return Connection
     * @throws \RuntimeException
     */
    public function getConnection()
    {
        if (!$this->connection) {
            throw new \RuntimeException('A database connection is required');
        }

        return $this->connection;
    }

    /**
     * @param $sql
     * @return int
     */
    public function exec($sql)
    {
        return $this->getConnection()->exec($sql);
    }

    /**
     * @param $sql
     * @return \PDOStatement
     */
    public function query($sql)
    {
        return $this->getConnection()->query($sql);
    }

    /**
     *
     */
    public function down() {}
}