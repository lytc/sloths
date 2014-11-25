<?php

namespace SlothsTest\Db\Table\Sql;

use Sloths\Db\Connection;
use Sloths\Db\Table\Sql\SqlTrait;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Table\Sql\SqlTrait
 */
class SqlTraitTest extends TestCase
{
    public function test()
    {
        $sql = new Sql();
        $connection = new Connection('dsn');
        $sql->setConnection($connection);
        $this->assertSame($connection, $sql->getConnection());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetConnectionShouldThrowAnExceptionIfHasNoConnectionWithStrictMode()
    {
        $sql = new Sql();
        $sql->getConnection();
    }
}

class Sql
{
    use SqlTrait;
}