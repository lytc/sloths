<?php
/**
 * Created by PhpStorm.
 * User: lytc
 * Date: 4/20/14
 * Time: 11:29 AM
 */
namespace SlothsTest\Db;

use Sloths\Db\Connection;
use Sloths\Db\Db;
use Sloths\Db\Sql\Select;

/**
 * @covers \Sloths\Db\Connection
 */
class ConnectionTest extends TestCase
{
    public function testGetPdoShouldReturnsTheSamePdoInstance()
    {
        $connection = new Connection('', '', '', '', '');
        $pdo = new PDOMock();
        $connection->setPdo($pdo);
        $this->assertSame($pdo, $connection->getPdo());
        $this->assertSame($connection->getPdo(), $connection->getPdo());
    }

    public function testGetPdo()
    {
        $connection = new Connection('', '', '', '', '');
        $connection->setPdoClass('SlothsTest\Db\PDOMock');
        $this->assertInstanceOf('SlothsTest\Db\PDOMock', $connection->getPdo());
    }

    public function testQuote()
    {
        $connection = $this->createConnection();
        $this->assertSame(1, $connection->quote(true));
        $this->assertSame(0, $connection->quote(false));
        $this->assertSame(2, $connection->quote(2));
        $this->assertSame(2.1, $connection->quote(2.1));
        $this->assertSame('NULL', $connection->quote(null));
        $this->assertSame([1, 0, 2, 2.1, 'NULL'], $connection->quote([true, false, 2, 2.1, null]));
        $this->assertSame("foo'bar", $connection->quote(Db::expr("foo'bar")));
        $this->assertSame("SELECT foo.* FROM foo", $connection->quote(new Select("foo")));
    }

    public function testQuoteWithType()
    {
        $pdo = $this->mockPdo('quote');
        $pdo->expects($this->at(0))->method('quote')->with(1, \PDO::PARAM_INT)->willReturn(1);
        $pdo->expects($this->at(1))->method('quote')->with('foo', \PDO::PARAM_STR)->willReturn("'foo'");
        $pdo->expects($this->at(2))->method('quote')->with('bar', \PDO::PARAM_STR)->willReturn("'bar'");

        $connection = $this->createConnection($pdo);
        $this->assertSame(1, $connection->quote(1, \PDO::PARAM_INT));

        $this->assertSame(["'foo'", "'bar'"], $connection->quote(['foo', 'bar'], \PDO::PARAM_STR));
    }

    public function testQuoteWithStringTypeShouldFallbackToPdoQuote()
    {
        $pdo = $this->mockPdo('quote');
        $pdo->expects($this->once())->method('quote')->with('foo')->willReturn("'foo'");
        $connection = $this->createConnection($pdo);
        $this->assertSame("'foo'", $connection->quote('foo'));
    }

    public function testEscape()
    {
        $connection = $this->createConnection();
        $this->assertSame(1, $connection->escape(true));
        $this->assertSame(0, $connection->escape(false));
        $this->assertSame(2, $connection->escape(2));
        $this->assertSame(2.1, $connection->escape(2.1));
        $this->assertSame('NULL', $connection->escape(null));
        $this->assertSame([1, 0, 2, 2.1, 'NULL'], $connection->escape([true, false, 2, 2.1, null]));

        $pdo = $this->mockPdo('quote');
        $pdo->expects($this->any())->method('quote')->with("foo'bar")->willReturn("'foo\'bar'");
        $connection = $this->createConnection($pdo);
        $this->assertSame("foo\'bar", $connection->escape("foo'bar"));

        $values = [true, false, 0, 2, 2.1, null, "foo'bar"];
        $quoted = $connection->quote($values);

        foreach ($values as $index => $val) {
            if (is_string($val)) {
                $this->assertSame($connection->escape($val), substr($quoted[$index], 1, -1));
            } else {
                $this->assertSame($connection->escape($val), $quoted[$index]);
            }
        }
    }

    public function testInsertShouldReturnsInsertedId()
    {
        $pdo = $this->mockPdo('exec', 'lastInsertId');
        $pdo->expects($this->once())->method('exec')->willReturn(1);
        $pdo->expects($this->once())->method('lastInsertId')->willReturn(1);
        $connection = $this->createConnection($pdo);

        $insert = $this->getMock('\Sloths\Db\Sql\Insert', ['toString']);
        $insert->expects($this->once())->method('toString');

        $this->assertSame(1, $connection->insert($insert));
    }

    public function testUpdateShouldReturnsAffectedRows()
    {
        $pdo = $this->mockPdo('exec');
        $pdo->expects($this->once())->method('exec')->willReturn(2);
        $connection = $this->createConnection($pdo);

        $update = $this->getMock('\Sloths\Db\Sql\Update', ['toString']);
        $update->expects($this->once())->method('toString');

        $this->assertSame(2, $connection->update($update));
    }

    public function testDeleteShouldReturnsAffectedRows()
    {
        $pdo = $this->mockPdo('exec');
        $pdo->expects($this->once())->method('exec')->willReturn(2);
        $connection = $this->createConnection($pdo);

        $delete = $this->getMock('\Sloths\Db\Sql\Delete', ['toString']);
        $delete->expects($this->once())->method('toString');

        $this->assertSame(2, $connection->delete($delete));
    }

    public function testSelect()
    {
        $stmt = $this->getMock('PDOStatement', ['fetch']);
        $stmt->expects($this->once())->method('fetch')->willReturn([]);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->once())->method('query')->willReturn($stmt);

        $connection = $this->createConnection($pdo);

        $select = $this->getMock('\Sloths\Db\Sql\Select', ['toString']);
        $select->expects($this->once())->method('toString');

        $this->assertSame([], $connection->select($select));
    }

    public function testSelectAll()
    {
        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn([[]]);
        $pdo = $this->mockPdo('query');
        $pdo->expects($this->once())->method('query')->willReturn($stmt);

        $connection = $this->createConnection($pdo);

        $select = $this->getMock('\Sloths\Db\Sql\Select', ['toString']);
        $select->expects($this->once())->method('toString');

        $this->assertSame([[]], $connection->selectAll($select));
    }

    public function testSelectAllWithFoundRows()
    {
        $select = new Select();
        $select->from('foo')->limit(1);

        $stmt1 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn([]);

        $stmt2 = $this->getMock('PDOStatement', ['fetchColumn']);
        $stmt2->expects($this->once())->method('fetchColumn')->willReturn(10);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->at(0))->method('query')->with('SELECT SQL_CALC_FOUND_ROWS foo.* FROM foo LIMIT 1')->willReturn($stmt1);
        $pdo->expects($this->at(1))->method('query')->with('SELECT FOUND_ROWS()')->willReturn($stmt2);

        $connection = $this->createConnection($pdo);
        $this->assertSame(['rows' => [], 'foundRows' => 10], $connection->selectAllWithFoundRows($select));
    }

    public function testSelectColumn()
    {
        $select = new Select('foo');
        $stmt = $this->getMock('PDOStatement', ['fetchColumn']);
        $stmt->expects($this->at(0))->method('fetchColumn')->with(0)->willReturn('bar');
        $stmt->expects($this->at(1))->method('fetchColumn')->with(1)->willReturn('baz');

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->exactly(2))->method('query')->with($select->toString())->willReturn($stmt);

        $connection = $this->createConnection($pdo);
        $this->assertSame('bar', $connection->selectColumn($select));
        $this->assertSame('baz', $connection->selectColumn($select, 1));
    }

    public function testNestedTransactionWithCommit()
    {
        $pdo = $this->mockPdo('beginTransaction', 'commit');
        $pdo->expects($this->once())->method('beginTransaction')->willReturn(true);
        $pdo->expects($this->once())->method('commit')->willReturn(true);
        $connection = $this->createConnection($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->commit();
        $connection->commit();
    }

    public function testNestedTransactionWithRollBack()
    {
        $pdo = $this->mockPdo('beginTransaction', 'rollBack');
        $pdo->expects($this->once())->method('beginTransaction')->willReturn(true);
        $pdo->expects($this->once())->method('rollBack')->willReturn(true);
        $connection = $this->createConnection($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->rollBack();
        $connection->rollBack();
    }

    public function testNestedTransactionWithRollBackAndCommit()
    {
        $pdo = $this->mockPdo('beginTransaction', 'commit', 'rollBack');
        $pdo->expects($this->once())->method('beginTransaction')->willReturn(true);
        $pdo->expects($this->never())->method('commit');
        $pdo->expects($this->once())->method('rollBack')->willReturn(true);
        $connection = $this->createConnection($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->commit();
        $connection->rollBack();
    }

    public function testTransactionShouldRunAndTheScopeOfClosureCallbackShouldBeConnection()
    {
        $pdo = $this->mockPdo('beginTransaction', 'commit', 'rollBack');
        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('commit');
        $pdo->expects($this->never())->method('rollBack');

        $connection = $this->createConnection($pdo);

        $result = $connection->transaction(function () {
            return $this;
        });

        $this->assertSame($connection, $result);
    }

    public function testTransactionShouldAllowCallable()
    {
        $pdo = $this->mockPdo('beginTransaction', 'commit', 'rollBack');
        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('commit');
        $pdo->expects($this->never())->method('rollBack');

        $connection = $this->createConnection($pdo);

        $foo = $this->getMock('Foo', ['bar']);
        $foo->expects($this->once())->method('bar')->with($connection)->willReturn($connection);

        $result = $connection->transaction([$foo, 'bar']);

        $this->assertSame($connection, $result);
    }

    public function testTransactionShouldRollbackWhenThrowAnException()
    {
        $pdo = $this->mockPdo('beginTransaction', 'commit', 'rollBack');
        $pdo->expects($this->once())->method('beginTransaction');
        $pdo->expects($this->once())->method('rollBack');
        $pdo->expects($this->never())->method('commit');

        $connection = $this->createConnection($pdo);

        try {
            $connection->transaction(function () {
                throw new \Exception('foo');
            });
        } catch (\Exception $e) {
            $this->assertSame('foo', $e->getMessage());
        }
    }

    public function test__sleep()
    {
        $connection = new Connection('host', 'port', 'username', 'pass', 'dbName');
        $connection2 = serialize($connection);
        $connection2 = unserialize($connection2);
        $this->assertSame('host', $connection2->getHost());
        $this->assertSame('port', $connection2->getPort());
        $this->assertSame('username', $connection2->getUsername());
        $this->assertSame('pass', $connection2->getPassword());
        $this->assertSame('dbName', $connection2->getDbName());
    }
}