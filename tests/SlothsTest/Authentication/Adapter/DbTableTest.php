<?php

namespace SlothsTest\Authentication\Adapter;

use Sloths\Authentication\Adapter\DbTable;
use Sloths\Authentication\Result;
use SlothsTest\Db\TestCase;

/**
 * @covers Sloths\Authentication\Adapter\DbTable
 * @covers Sloths\Authentication\Adapter\AbstractDb
 */
class DbTableTest extends TestCase
{
    public function testInstanceWithDefaultTableNameIdentityColumnAndCredentialColumn()
    {
        $connection = $this->mockConnection();
        $adapter = new DbTable($connection);

        $this->assertSame(DbTable::DEFAULT_TABLE_NAME, $adapter->getTableName());
        $this->assertSame(DbTable::DEFAULT_IDENTITY_COLUMN, $adapter->getIdentityColumn());
        $this->assertSame(DbTable::DEFAULT_CREDENTIAL_COLUMN, $adapter->getCredentialColumn());
    }

    public function testInstanceWithTableNameIdentityColumnAndCredentialColumn()
    {
        $connection = $this->mockConnection();
        $adapter = new DbTable($connection, 'foo', 'bar', 'baz');

        $this->assertSame('foo', $adapter->getTableName());
        $this->assertSame('bar', $adapter->getIdentityColumn());
        $this->assertSame('baz', $adapter->getCredentialColumn());
    }

    /**
     * @dataProvider dataProviderAuthenticate
     */
    public function testAuthenticate($expectedResultCode, $data)
    {
        $stmt = $this->getMock('PDOStatement', ['fetch']);
        $connection = $this->mockConnection('query');
        $adapter = new DbTable($connection);

        $stmt->expects($this->once())->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn($data);

        $connection->expects($this->once())->method('query')
            ->with(sprintf("SELECT %1\$s.* FROM %1\$s WHERE (%2\$s = 'foo')", $adapter->getTableName(), $adapter->getIdentityColumn()))
            ->willReturn($stmt);

        $adapter->setIdentity('foo');
        $adapter->setCredential('bar');

        $result = $adapter->authenticate();
        $this->assertInstanceOf('Sloths\Authentication\Result', $result);
        $this->assertSame($expectedResultCode, $result->getCode());
    }

    public function dataProviderAuthenticate()
    {
        return [
            [Result::SUCCESS, [DbTable::DEFAULT_IDENTITY_COLUMN => 'foo', DbTable::DEFAULT_CREDENTIAL_COLUMN => 'bar']],
            [Result::ERROR_IDENTITY_NOT_FOUND, false],
            [Result::ERROR_CREDENTIAL_INVALID, [DbTable::DEFAULT_IDENTITY_COLUMN => 'foo', DbTable::DEFAULT_CREDENTIAL_COLUMN => 'baz']]
        ];
    }
}