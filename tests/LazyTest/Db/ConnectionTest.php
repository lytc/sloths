<?php

namespace LazyTest\Db;
use Lazy\Db\Connection;
use Lazy\Db\Expr;

/**
 * @covers Lazy\Db\Connection
 */
class ConnectionTest extends TestCase
{
    public function testEnv()
    {
        $this->assertSame(Connection::ENV_TEST, Connection::getEnv());
        Connection::setEnv(Connection::ENV_PRODUCTION);
        $this->assertSame(Connection::ENV_PRODUCTION, Connection::getEnv());
        Connection::setEnv(Connection::ENV_TEST);
    }

    public function testDefaultConfig()
    {
        $config = array(
            'development' => array(
                'dsn' => 'sqlite::memory:'
            ),
            'test' => array(
                'dsn' => 'sqlite::memory:'
            ),
            'production' => array(
                'dsn' => 'sqlite::memory:'
            )
        );

        $prevConfig = Connection::getDefaultConfig();
        Connection::setDefaultConfig($config);
        $this->assertSame($config, Connection::getDefaultConfig());
        Connection::setDefaultConfig($prevConfig);
    }

    public function testConfig()
    {
        $config = array(
            'development' => array(
                'dsn' => 'sqlite::memory:'
            ),
            'test' => array(
                'dsn' => 'sqlite::memory:'
            ),
            'production' => array(
                'dsn' => 'sqlite::memory:'
            )
        );

        Connection::setConfig('foo', $config);
        $this->assertSame($config, Connection::getConfig('foo'));
    }

    public function testGetDefaultInstance()
    {
        $connection = Connection::getDefaultInstance();
        $this->assertInstanceOf('Lazy\Db\Connection', $connection);
        $this->assertInstanceOf('PDO', $connection);
        $this->assertSame($connection, Connection::getDefaultInstance());
    }

    public function testGetInstance()
    {
        Connection::setConfig('foo', array(
            'test' => 'sqlite::memory:'
        ));

        $connection = Connection::getInstance('foo');
        $this->assertInstanceOf('Lazy\Db\Connection', $connection);
        $this->assertSame($connection, Connection::getInstance('foo'));
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Connection configuration was not set
     */
    public function testGetConnectionShouldThrowAnExceptionIfHasNoConfiguration()
    {
        Connection::getInstance('bar');
    }

    public function testQuote()
    {
        $connection = $this->connection;
        $expr = new Expr('NOW()');
        $this->assertSame($expr->toString(), $connection->quote($expr));
        $this->assertSame('NULL', $connection->quote(null));
        $this->assertSame(array(1, "'foo'"), $connection->quote(array(1, 'foo')));
    }
//
    public function testEscape()
    {
        $connection = $this->connection;
        $expr = new Expr("foo'bar");
        $this->assertSame($expr->toString(), $connection->escape($expr));
        $this->assertSame("foo\'bar", $connection->escape("foo'bar"));
    }
}