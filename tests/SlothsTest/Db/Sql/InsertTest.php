<?php

namespace SlothsTest\Db\Sql;
use Sloths\Db\Sql\Insert;
use Sloths\Db\Sql\Select;

/**
 * @covers \Sloths\Db\Sql\Insert<extended>
 */
class InsertTest extends \PHPUnit_Framework_TestCase
{
    public function testSelect()
    {

        $select = new Select('bar');
        $select->select('foo, bar');

        $insert = new Insert('foo');
        $insert->columns('foo, bar')->select($select);

        $expected = "INSERT INTO foo (foo, bar) SELECT foo, bar FROM bar";
        $this->assertSame($expected, $insert->toString());
    }

    /**
     * @covers \Sloths\Db\Sql\Insert::ignore
     * @covers \Sloths\Db\Sql\Replace::toggleOption
     */
    public function testIgnore()
    {
        $insert = new Insert('foo');
        $insert->ignore()->values(['foo' => 'foo']);

        $expected = "INSERT IGNORE INTO foo (foo) VALUES ('foo')";
        $this->assertSame($expected, $insert->toString());
    }

    public function testHighPriority()
    {
        $insert = new Insert('foo');
        $insert->highPriority()->values(['foo' => 'foo']);

        $expected = "INSERT HIGH_PRIORITY INTO foo (foo) VALUES ('foo')";
        $this->assertSame($expected, $insert->toString());

        $insert->highPriority(false);
        $expected = "INSERT INTO foo (foo) VALUES ('foo')";
        $this->assertSame($expected, $insert->toString());
    }

    public function testOnDuplicateKeyUpdate()
    {
        $insert = new Insert('foo');
        $insert->values(['foo' => 'foo'])->onDuplicateKeyUpdate(['foo' => 'bar', 'bar' => 'baz']);
        $expected = "INSERT INTO foo (foo) VALUES ('foo') ON DUPLICATE KEY UPDATE foo = 'bar', bar = 'baz'";
        $this->assertSame($expected, $insert->toString());
    }
}