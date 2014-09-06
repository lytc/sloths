<?php

namespace MigrationStub;

use Sloths\Db\Migration\AbstractMigration;

class Bar extends AbstractMigration
{
    public function up()
    {
        $this->exec('bar up');
    }

    public function down()
    {
        $this->exec('bar down');
    }
}