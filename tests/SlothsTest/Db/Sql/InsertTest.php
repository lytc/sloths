<?php

namespace SlothsTest\Db\Sql;

use Sloths\Db\Sql\Insert;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\Insert
 */
class InsertTest extends TestCase
{
    public function testWithArrayValues()
    {
        $insert = new Insert();
        $insert->table('users');
        $insert->values(['username' => 'Ly Tran', 'email' => 'lytc@example.com']);

        $expected = "INSERT INTO users SET `username` = 'Ly Tran', `email` = 'lytc@example.com'";
        $this->assertSame($expected, $insert->toString());
    }
}