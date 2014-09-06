<?php

namespace SlothsTest\Db;

use Sloths\Db\Connection;
use Sloths\Db\Database;
use Sloths\Db\Sql\Insert;
use Sloths\Db\Sql\Spec\Raw;
use SlothsTest\TestCase;

/**
 * @covers \Sloths\Db\Database
 */
class DatabaseTest extends TestCase
{
    public function testConnection()
    {
        $connection = new Connection('dsn');
        $db = new Database();
        $db->setConnection($connection);

        $this->assertSame($connection, $db->getConnection());
        $this->assertSame($connection, $db->getReadConnection());
        $this->assertSame($connection, $db->getWriteConnection());

        $readConnection = new Connection('read');
        $writeConnection = new Connection('write');

        $db->setReadConnection($readConnection);
        $db->setWriteConnection($writeConnection);

        $this->assertSame($readConnection, $db->getReadConnection());
        $this->assertSame($writeConnection, $db->getWriteConnection());
    }

    /**
     * @expectedException \DomainException
     */
    public function testGetConnectionShouldThrowAnExceptionIfHaveNoConnectionWithStrictMode()
    {
        $db = new Database();
        $db->getConnection();
    }

    /**
     * @expectedException \DomainException
     */
    public function testGetReadConnectionShouldThrowAnExceptionIfHaveNoConnectionWithStrictMode()
    {
        $db = new Database();
        $db->getReadConnection();
    }

    /**
     * @expectedException \DomainException
     */
    public function testGetWriteConnectionShouldThrowAnExceptionIfHaveNoConnectionWithStrictMode()
    {
        $db = new Database();
        $db->getWriteConnection();
    }

    public function testRunWithSingleConnection()
    {
        $readSql = $this->getMock('Sloths\Db\Sql\SqlReadInterface');
        $readSql->expects($this->once())->method('toString')->willReturn('read sql');

        $writeSql = $this->getMock('Sloths\Db\Sql\SqlWriteInterface');
        $writeSql->expects($this->once())->method('toString')->willReturn('write sql');

        $connection = $this->getMock('Sloths\Db\Connection', ['query', 'exec'], ['dsn']);
        $connection->expects($this->once())->method('query')->with('read sql');
        $connection->expects($this->once())->method('exec')->with('write sql');

        $database = new Database();
        $database->setConnection($connection);

        $database->run($readSql);
        $database->run($writeSql);
    }

    public function testRunWithReadAndWriteConnection()
    {
        $readSql = $this->getMock('Sloths\Db\Sql\SqlReadInterface');
        $readSql->expects($this->once())->method('toString')->willReturn('read sql');

        $writeSql = $this->getMock('Sloths\Db\Sql\SqlWriteInterface');
        $writeSql->expects($this->once())->method('toString')->willReturn('write sql');

        $readConnection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $readConnection->expects($this->once())->method('query')->with('read sql');

        $writeConnection = $this->getMock('Sloths\Db\Connection', ['exec'], ['dsn']);
        $writeConnection->expects($this->once())->method('exec')->with('write sql');

        $database = new Database();
        $database->setReadConnection($readConnection);
        $database->setWriteConnection($writeConnection);

        $database->run($readSql);
        $database->run($writeSql);
    }

    public function testRunSqlInsert()
    {
        $database = new Database();

        $connection = $this->getMock('Sloths\Db\Connection', ['exec', 'getLastInsertId'], ['dsn']);
        $connection->expects($this->once())->method('exec');
        $connection->expects($this->once())->method('getLastInsertId')->willReturn('id');

        $database->setConnection($connection);

        $insert = new Insert();
        $this->assertSame('id', $database->run($insert));
    }

    public function testRaw()
    {
        $database = new Database();
        $this->assertInstanceOf('Sloths\Db\Sql\Spec\Raw', $database->raw('expr'));

        $raw = new Raw('expr');
        $this->assertSame($raw, $database->raw($raw));
    }

    /**
     * @dataProvider dataProviderTestEscape
     */
    public function testEscape($input, $expected)
    {
        $this->assertSame($expected, Database::escape($input));
    }

    public function dataProviderTestEscape()
    {
        $sql = $this->getMock('Sloths\Db\Sql\SqlInterface');
        return [
            [1, 1],
            [true, true],
            [null, null],
            [$sql, $sql],
            ['foo"bar', 'foo\"bar'],
            [[1, true, null, $sql, 'foo"bar'], [1, true, null, $sql, 'foo\"bar']]
        ];
    }

    /**
     * @dataProvider dataProviderTestQuote
     */
    public function testQuote($expected, $input)
    {
        $this->assertSame($expected, Database::quote($input));
    }

    public function dataProviderTestQuote()
    {
        $raw = Database::raw('foo');
        return [
            [1, 1],
            ['1', '1'],
            ["'foo'", 'foo'],
            [true, true],
            ['NULL', null],
            [$raw, $raw],
            [["'foo'"], ['foo']]
        ];
    }

    /**
     * @dataProvider dataProviderTestBind
     */
    public function testBind($expected, $expr, $params)
    {
        $this->assertSame($expected, Database::bind($expr, $params));
    }

    public function dataProviderTestBind()
    {
        return [
            ["foo = 1", 'foo', 1],
            ["foo = 1", 'foo = ?', 1],
            ["foo != 1", 'foo != ?', 1],
            ["foo = 'foo'", 'foo', 'foo'],
            ["foo IS NULL", 'foo', null],
            ["foo IS NOT NULL", 'foo != ?', null],
            ["foo IN (1, 2, 3)", 'foo IN (?)', [1, 2, 3]],
            ["foo NOT IN (1, 2, 3)", 'foo NOT IN (?)', [1, 2, 3]],
            ["foo IN (SELECT 1, 2, 3)", 'foo IN(?)', Database::raw('SELECT 1, 2, 3')],
            ["foo LIKE '%foo%'", "foo LIKE %?%", 'foo'],
            ["foo LIKE '%foo'", "foo LIKE %?", 'foo'],
            ["foo LIKE 'foo%'", "foo LIKE ?%", 'foo'],
            ["foo = 1 bar = 'bar'", 'foo = ? bar = ?', [1, 'bar']],
            ["foo = 1 bar = 'bar'", 'foo = :foo bar = :bar', ['foo' => 1, 'bar' => 'bar']]
        ];
    }
}