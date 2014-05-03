<?php

namespace LazyTest\Db;
use Lazy\Db\Db;
use Lazy\Db\Sql\Select;

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

        $pdo = $this->mockPdo();
        $pdo->shouldReceive('quote')->with('foo"bar')->andReturn("'foo\\\"bar'");

        Db::setQuoter([$pdo, 'quote']);
        $this->assertSame("foo\\\"bar", Db::escape('foo"bar'));

        Db::setQuoter(Db::getDefaultQuoter());
    }

    public function testSetQuoter()
    {
        $pdo = $this->mockPdo();
        $pdo->shouldReceive('quote')->with('foo')->andReturn("'foo'");

        Db::setQuoter([$pdo, 'quote']);
        $this->assertSame("'foo'", Db::quote('foo'));

        Db::setQuoter(Db::getDefaultQuoter());
    }
}