<?php

namespace LazyTest\Db;

use Lazy\Db\Connection;

class TestCase extends \PHPUnit_Framework_TestCase
{
    protected $connection;

    protected function setUp()
    {
        $this->connection = Connection::getDefaultInstance();
    }

    protected function getMockConnection(array $methods)
    {
        $defaultConfig = Connection::getDefaultConfig();
        $envConfig = $defaultConfig[Connection::getEnv()];
        return $this->getMock('Lazy\Db\Connection', $methods, $envConfig);
    }
}