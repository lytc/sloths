<?php

namespace SlothsTest\Db;
use Sloths\Db\Db;
use Sloths\Db\Sql\Delete;
use Sloths\Db\Sql\Insert;
use Sloths\Db\Sql\Select;
use Sloths\Db\Sql\Update;

/**
 * @covers \Sloths\Db\Db
 */
class DbTest extends TestCase
{
    public function testQuoteIdentifier()
    {
        $this->assertSame('`foo`', Db::quoteIdentifier('foo'));
        $this->assertSame('`foo`.`bar`', Db::quoteIdentifier('foo.bar'));
        $this->assertSame(['`foo`', '`bar`'], Db::quoteIdentifier(['foo', 'bar']));
    }

    public function testQuote()
    {
        $this->assertSame(1, Db::quote(1));
        $this->assertSame(1, Db::quote(true));
        $this->assertSame(0, Db::quote(false));
        $this->assertSame('NULL', Db::quote(null));
        $this->assertSame("'foo\\'bar'", Db::quote("foo'bar"));
        $this->assertSame([1, 1, 0, 'NULL', "'foo\\'bar'"], Db::quote([1, 1, 0, null, "foo'bar"]));
        $this->assertSame("foo'bar", Db::quote(Db::expr("foo'bar")));
        $this->assertSame("SELECT foo.* FROM foo", Db::quote(new Select("foo")));
    }

    public function testEscape()
    {
        $this->assertSame(1, Db::escape(1));

        $pdo = $this->mockPdo('quote');
        $pdo->expects($this->once())->method('quote')->with('foo"bar')->willReturn("'foo\\\"bar'");

        Db::setQuoter([$pdo, 'quote']);
        $this->assertSame("foo\\\"bar", Db::escape('foo"bar'));

        Db::setQuoter(Db::getDefaultQuoter());
    }

    public function testSetQuoter()
    {
        $pdo = $this->mockPdo('quote');
        $pdo->expects($this->once())->method('quote')->with('foo')->willReturn("'foo'");

        Db::setQuoter([$pdo, 'quote']);
        $this->assertSame("'foo'", Db::quote('foo'));

        Db::setQuoter(Db::getDefaultQuoter());
    }

    public function testMethodSelect()
    {
        $select = new Select();
        $select->from('foo')->select('bar');

        $this->assertEquals($select->toString(), Db::select('foo', 'bar')->toString());
    }

    public function testMethodInsert()
    {
        $insert = new Insert();
        $insert->into('foo')->values(['foo' => 'bar']);

        $this->assertEquals($insert->toString(), Db::insert('foo', ['foo' => 'bar'])->toString());
    }

    public function testMethodUpdate()
    {
        $update = new Update();
        $update->from('foo')->set(['foo' => 'bar'])->where('bar = 1');

        $this->assertEquals($update->toString(), Db::update('foo', ['foo' => 'bar'], 'bar = 1')->toString());
    }

    public function testMethodDelete()
    {
        $delete = new Delete();
        $delete->from('foo')->where('bar = 1');

        $this->assertEquals($delete->toString(), Db::delete('foo', 'bar = 1')->toString());
    }
}