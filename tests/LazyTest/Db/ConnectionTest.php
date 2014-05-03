<?php
/**
 * Created by PhpStorm.
 * User: lytc
 * Date: 4/20/14
 * Time: 11:29 AM
 */
namespace LazyTest\Db;

use Lazy\Db\Connection;
use Lazy\Db\Db;
use Lazy\Db\Sql\Select;

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
        $connection->setPdoClass('LazyTest\Db\PDOMock');
        $this->assertInstanceOf('LazyTest\Db\PDOMock', $connection->getPdo());
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
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('quote')->once()->with(1, \PDO::PARAM_INT)->andReturn(1);
        $pdo->shouldReceive('quote')->once()->with('foo', \PDO::PARAM_STR)->andReturn("'foo'");
        $pdo->shouldReceive('quote')->once()->with('bar', \PDO::PARAM_STR)->andReturn("'bar'");

        $connection = $this->createConnection($pdo);
        $this->assertSame(1, $connection->quote(1, \PDO::PARAM_INT));

        $this->assertSame(["'foo'", "'bar'"], $connection->quote(['foo', 'bar'], \PDO::PARAM_STR));
    }

    public function testQuoteWithStringTypeShouldFallbackToPdoQuote()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('quote')->with('foo')->andReturn("'foo'");
        $connection = $this->createConnection($pdo);
        $this->assertSame($pdo->quote('foo'), $connection->quote('foo'));
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

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('quote')->with("foo'bar")->andReturn("'foo\'bar'");
        $connection = $this->createConnection($pdo);
        $this->assertSame(substr($connection->quote("foo'bar"), 1, -1), $connection->escape("foo'bar"));

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
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('exec')->once()->andReturn(1);
        $pdo->shouldReceive('lastInsertId')->once()->andReturn(1);
        $connection = $this->createConnection($pdo);

        $insert = $this->mock('\Lazy\Db\Sql\Insert');
        $insert->shouldReceive('toString');

        $this->assertSame(1, $connection->insert($insert));
    }

    public function testUpdateShouldReturnsAffectedRows()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('exec')->once()->andReturn(2);
        $connection = $this->createConnection($pdo);

        $update = $this->mock('\Lazy\Db\Sql\Update');
        $update->shouldReceive('toString')->once();

        $this->assertSame(2, $connection->update($update));
    }

    public function testDeleteShouldReturnsAffectedRows()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('exec')->once()->andReturn(2);
        $connection = $this->createConnection($pdo);

        $delete = $this->mock('\Lazy\Db\Sql\Delete');
        $delete->shouldReceive('toString')->once();

        $this->assertSame(2, $connection->delete($delete));
    }

    public function testSelect()
    {
        $stmt = $this->mockPdo('PDOStatement');
        $stmt->shouldReceive('fetch')->once()->andReturn([]);

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->once()->andReturn($stmt);

        $connection = $this->createConnection($pdo);

        $select = $this->mock('\Lazy\Db\Sql\Select');
        $select->shouldReceive('toString')->once();

        $this->assertSame([], $connection->select($select));
    }

    public function testSelectAll()
    {
        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn([[]]);
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->once()->andReturn($stmt);

        $connection = $this->createConnection($pdo);

        $select = $this->mock('\Lazy\Db\Sql\Select');
        $select->shouldReceive('toString');

        $this->assertSame([[]], $connection->selectAll($select));
    }

    public function testSelectAllWithFoundRows()
    {
        $select = new Select();
        $select->from('foo')->limit(1);

        $stmt1 = $this->mock('PDOStatement');
        $stmt1->shouldReceive('fetchAll')->once()->with(\PDO::FETCH_ASSOC)->andReturn([]);

        $stmt2 = $this->mock('PDOStatement');
        $stmt2->shouldReceive('fetchColumn')->once()->andReturn(10);

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->once()->with('SELECT SQL_CALC_FOUND_ROWS foo.* FROM foo LIMIT 1')->andReturn($stmt1);
        $pdo->shouldReceive('query')->once()->with('SELECT FOUND_ROWS()')->andReturn($stmt2);

        $connection = $this->createConnection($pdo);
        $this->assertSame(['rows' => [], 'foundRows' => 10], $connection->selectAllWithFoundRows($select));
    }

    public function testSelectColumn()
    {
        $select = new Select('foo');
        $stmt = $this->mock('PDOStatement');
        $stmt->shouldReceive('fetchColumn')->once()->with(0)->andReturn('bar');
        $stmt->shouldReceive('fetchColumn')->once()->with(1)->andReturn('baz');

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('query')->twice()->with($select->toString())->andReturn($stmt);

        $connection = $this->createConnection($pdo);
        $this->assertSame('bar', $connection->selectColumn($select));
        $this->assertSame('baz', $connection->selectColumn($select, 1));
    }

    public function testNestedTransactionWithCommit()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('beginTransaction')->once()->andReturn(true);
        $pdo->shouldReceive('commit')->once()->andReturn(true);
        $connection = $this->createConnection($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->commit();
        $connection->commit();
    }

    public function testNestedTransactionWithRollBack()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('beginTransaction')->once()->andReturn(true);
        $pdo->shouldReceive('rollBack')->once()->andReturn(true);
        $connection = $this->createConnection($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->rollBack();
        $connection->rollBack();
    }

    public function testNestedTransactionWithRollBackAndCommit()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('beginTransaction')->once()->andReturn(true);
        $pdo->shouldReceive('commit')->never();
        $pdo->shouldReceive('rollBack')->once()->andReturn(true);
        $connection = $this->createConnection($pdo);

        $connection->beginTransaction();
        $connection->beginTransaction();
        $connection->commit();
        $connection->rollBack();
    }

    public function testTransactionShouldRunAndTheScopeOfClosureCallbackShouldBeConnection()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('beginTransaction')->once();
        $pdo->shouldReceive('commit')->once();
        $pdo->shouldReceive('rollBack')->never();

        $connection = $this->createConnection($pdo);

        $result = $connection->transaction(function () {
            return $this;
        });

        $this->assertSame($connection, $result);
    }

    public function testTransactionShouldAllowCallable()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('beginTransaction')->once();
        $pdo->shouldReceive('commit')->once();
        $pdo->shouldReceive('rollBack')->never();

        $connection = $this->createConnection($pdo);

        $foo = $this->mock('Foo');
        $foo->shouldReceive('bar')->once()->with($connection)->andReturn($connection);

        $result = $connection->transaction([$foo, 'bar']);

        $this->assertSame($connection, $result);
    }

    public function testTransactionShouldRollbackWhenThrowAnException()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('beginTransaction')->once();
        $pdo->shouldReceive('rollBack')->once();
        $pdo->shouldReceive('commit')->never();

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