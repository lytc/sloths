<?php

namespace SlothsTest\Db;

use Sloths\Cache\CacheManager;
use Sloths\Db\Connection;
use Sloths\Db\ConnectionManager;
use SlothsTest\TestCase;
use Sloths\Db\Table;

/**
 * @covers Sloths\Db\Table
 */
class TableTest extends TestCase
{
    public function test()
    {
        $readConnection = new Connection('dsn');
        $writeConnection = new Connection('dsn');
        $cacheManager = new CacheManager();

        $connectionManager = new ConnectionManager();
        $connectionManager->setReadConnection($readConnection);
        $connectionManager->setWriteConnection($writeConnection);
        $connectionManager->setCacheManager($cacheManager);

        $table = new Table('users', 'id');
        $table->setConnectionManager($connectionManager);

        $select = $table->select('name');

        $this->assertSame($readConnection, $select->getConnection());
        $this->assertSame($cacheManager, $select->getCacheManager());
        $this->assertSame("SELECT users.name FROM users", $select->toString());

        $select = $table->selectById(1, 'name');
        $this->assertSame("SELECT users.name FROM users WHERE (users.id = 1)", $select->toString());

        $insert = $table->insert(['name' => 'foo']);
        $this->assertSame($writeConnection, $insert->getConnection());
        $this->assertSame("INSERT INTO users SET `name` = 'foo'", $insert->toString());

        $update = $table->update(['name' => 'foo']);
        $this->assertSame($writeConnection, $update->getConnection());
        $this->assertSame("UPDATE users SET `name` = 'foo'", $update->toString());

        $delete = $table->delete('id', 1);
        $this->assertSame($writeConnection, $delete->getConnection());
        $this->assertSame("DELETE FROM users WHERE (`id` = 1)", $delete->toString());
    }

    /**
     * @expectedException \RuntimeException
     */
    public function testGetConnectionManagerShouldThrowAnExceptionIfHasNoConnectionManagerWithStrictMode()
    {
        $table = new Table('foo');
        $table->getConnectionManager();
    }
}