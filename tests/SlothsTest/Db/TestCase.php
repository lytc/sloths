<?php

namespace SlothsTest\Db;
use Sloths\Db\Connection;

class TestCase extends \SlothsTest\TestCase
{
    protected function createConnection($pdo = null)
    {
        $connection = new Connection('', '', '', '', '');
        $connection->setPdo($pdo ? : $this->mockPdo());
        return $connection;
    }

    public function mockConnection()
    {
        return $this->mock('Sloths\Db\Connection', ['', '', '', '', '']);
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