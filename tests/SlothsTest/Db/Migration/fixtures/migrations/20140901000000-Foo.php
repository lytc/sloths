<?php

namespace MigrationStub;

use Sloths\Db\Migration\AbstractMigration;

class Foo extends AbstractMigration
{
    public function up()
    {
        $this->exec('foo up');
    }

    public function down()
    {
        $this->exec('foo down');
    }
}