<?php

namespace Lazy\Db\Migration;

use Lazy\Db\Connection;

abstract class AbstractMigration
{
    protected $connection;

    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    abstract public function up();
    public function down()
    {

    }
}