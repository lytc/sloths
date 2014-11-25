<?php

namespace SlothsTest\Db;

use Sloths\Db\Connection;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Connection
 */
class ConnectionTest extends TestCase
{
    public function test()
    {
        $pdoClassName = __NAMESPACE__ . '\StubPdo';
        $options = [];

        $connection = new Connection('dsn', 'username', 'password', $options);
        $connection->setPdoClassName($pdoClassName);

        $this->assertInstanceOf($pdoClassName, $connection->getPdo());
        $this->assertSame($connection->getPdo(), $connection->getPdo());

        $this->assertSame(['dsn', 'username', 'password', array_replace($connection->getDefaultOptions(), $options)],
            $connection->getPdo()->constructArgs);
    }

    public function testAliasToPdoMethods()
    {
        $pdo = $this->getMock('mockpdo', ['exec', 'query', 'lastInsertId']);
        $pdo->expects($this->once())->method('exec')->with('test exec');
        $pdo->expects($this->once())->method('query')->with('test query');
        $pdo->expects($this->once())->method('lastInsertId');

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->any())->method('getPdo')->willReturn($pdo);

        $connection->exec('test exec');
        $connection->query('test query');
        $connection->getLastInsertId();
    }

    public function testNestedTransactionWithCommit()
    {
        $pdo = $this->getMock('mockpdo', ['beginTransaction', 'commit']);
        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('commit');

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->any())->method('getPdo')->willReturn($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->commit();
        $connection->commit();
        $connection->commit();
    }

    public function testNestedTransactionWithRollback()
    {
        $pdo = $this->getMock('mockpdo', ['beginTransaction', 'rollBack']);
        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('rollBack');

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->any())->method('getPdo')->willReturn($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->rollBack();
        $connection->rollBack();
        $connection->rollBack();
    }

    public function testNestedTransaction()
    {
        $pdo = $this->getMock('mockpdo', ['beginTransaction', 'commit', 'rollBack']);
        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('rollBack');
        $pdo->expects($this->never())->method('commit');

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->any())->method('getPdo')->willReturn($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->commit();
        $connection->commit();
        $connection->rollBack();
    }

    public function testTransactionWithCallback()
    {
        $callback = function($conn) use (&$expectedConnection) {
            $expectedConnection = $conn;
            return 1;
        };

        $connection = $this->getMock('Sloths\Db\Connection', ['beginTransaction', 'commit'], ['dsn']);
        $connection->expects($this->once())->method('beginTransaction');
        $connection->expects($this->once())->method('commit');

        try {
            $result = $connection->transaction($callback);
        } catch (\Exception $e) {

        }

        $this->assertSame(1, $result);
        $this->assertSame($connection, $expectedConnection);
    }

    public function testTransactionWithCallbackShouldRollbackIfTheCallbackThrowAnException()
    {
        $callback = function($conn) use (&$expectedConnection) {
            $expectedConnection = $conn;
            throw new \Exception();
        };

        $connection = $this->getMock('Sloths\Db\Connection', ['beginTransaction', 'rollBack'], ['dsn']);
        $connection->expects($this->once())->method('beginTransaction');
        $connection->expects($this->once())->method('rollBack');

        try {
            $connection->transaction($callback);
        } catch (\Exception $e) {

        }

        $this->assertSame($connection, $expectedConnection);
    }
}

class StubPdo
{
    public $constructArgs;

    public function __construct()
    {
        $this->constructArgs = func_get_args();
    }
}