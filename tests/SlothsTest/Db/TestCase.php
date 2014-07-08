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

    public function mockPdo()
    {
        return $this->getMock(__NAMESPACE__ . '\PDOMock', func_get_args());
    }

    public function mockConnection()
    {
        return $this->getMock('Sloths\Db\Connection', func_get_args(), [], '', false);
    }
}

class PDOMock extends \PDO
{
    public function __construct() {}
}