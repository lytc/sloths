<?php

namespace LazyTest\Db;
use Lazy\Db\Connection;

class TestCase extends \LazyTest\TestCase
{
    protected function createConnection($pdo = null)
    {
        $connection = new Connection('', '', '', '', '');
        $connection->setPdo($pdo ? : $this->mockPdo());
        return $connection;
    }

    public function mockConnection()
    {
        return $this->mock('Lazy\Db\Connection', ['', '', '', '', '']);
    }

    public function mockPdo()
    {
        return $this->mock(new PDOMock, []);
    }
}

class PDOMock extends \PDO
{
    public function __construct() {}
}