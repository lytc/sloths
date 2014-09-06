<?php

namespace Sloths\Db\Migration;

use Sloths\Db\Connection;

interface MigrationInterface
{
    /**
     * @param Connection $connection
     * @return $this
     */
    public function setConnection(Connection $connection);

    /**
     * @return Database
     * @throws \RuntimeException
     */
    public function getConnection();

    /**
     * @return void
     */
    public function up();

    /**
     * @return void
     */
    public function down();
}