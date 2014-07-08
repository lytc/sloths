<?php

namespace SlothsTest\Authentication\Adapter;

use Sloths\Authentication\Adapter\DbModel;
use Sloths\Authentication\Result;
use Sloths\Db\Model\Model;
use SlothsTest\Db\TestCase;

/**
 * @covers Sloths\Authentication\Adapter\DbModel
 * @covers Sloths\Authentication\Adapter\AbstractDb
 */
class DbModelTest extends TestCase
{
    public function testInstanceWithDefaultTableNameIdentityColumnAndCredentialColumn()
    {
        $connection = $this->mockConnection();
        $adapter = new DbModel($connection);

        $this->assertSame(DbModel::DEFAULT_IDENTITY_COLUMN, $adapter->getIdentityColumn());
        $this->assertSame(DbModel::DEFAULT_CREDENTIAL_COLUMN, $adapter->getCredentialColumn());
    }

    public function testInstanceWithTableNameIdentityColumnAndCredentialColumn()
    {
        $adapter = new DbModel(__NAMESPACE__ . '\User', 'bar', 'baz');

        $this->assertSame(__NAMESPACE__ . '\User', $adapter->getModelClassName());
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
        User::setConnection($connection);

        $adapter = new DbModel(__NAMESPACE__ . '\User');

        $stmt->expects($this->once())->method('fetch')
            ->with(\PDO::FETCH_ASSOC)
            ->willReturn($data);

        $connection->expects($this->once())->method('query')
            ->with(sprintf("SELECT * FROM %s WHERE (%s = 'foo') LIMIT 1", User::getTableName(), $adapter->getIdentityColumn()))
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
            [Result::SUCCESS, [
                User::getPrimaryKey() => 1,
                DbModel::DEFAULT_IDENTITY_COLUMN => 'foo',
                DbModel::DEFAULT_CREDENTIAL_COLUMN => 'bar']],

            [Result::ERROR_IDENTITY_NOT_FOUND, false],

            [Result::ERROR_CREDENTIAL_INVALID, [
                User::getPrimaryKey() => 1,
                DbModel::DEFAULT_IDENTITY_COLUMN => 'foo',
                DbModel::DEFAULT_CREDENTIAL_COLUMN => 'baz']]
        ];
    }
}

class User extends Model
{
    protected static $columns = [
        'id' => self::INT,
        DbModel::DEFAULT_IDENTITY_COLUMN => self::VARCHAR,
        DbModel::DEFAULT_CREDENTIAL_COLUMN => self::VARCHAR
    ];
}