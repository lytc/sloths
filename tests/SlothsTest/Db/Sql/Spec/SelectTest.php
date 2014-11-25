<?php

namespace SlothsTest\Db\Sql\Spec;

use Sloths\Db\ConnectionManager;
use SlothsTest\TestCase;
use Sloths\Db\Sql\Spec\Select;

/**
 * @covers Sloths\Db\Sql\Spec\Select
 */
class SelectTest extends TestCase
{
    /**
     * @dataProvider dataProvider
     */
    public function test($expected, $table, $columns = null)
    {
        $select = new Select($table, $columns);
        $this->assertSame($expected, $select->toString());
    }

    public function dataProvider()
    {
        return [
            ["SELECT foo.* FROM foo", 'foo'],
            ["SELECT foo.* FROM foo", 'foo', '*'],
            ["SELECT f.* FROM foo AS f", 'foo f'],
            ["SELECT f.* FROM foo AS f", 'foo AS f'],
            ["SELECT f.* FROM foo AS f", 'foo as f'],
            ["SELECT f.* FROM foo AS f", ' foo   as   f '],
            ["SELECT foo.bar, foo.qux AS q FROM foo", 'foo', ['bar', 'q' => 'qux']],
            ["SELECT bar, qux AS q FROM foo", 'foo', 'bar, qux AS q'],
            ["SELECT f.bar, f.qux AS q FROM foo AS f", 'foo f', ['bar', 'q' => 'qux']],
            ["SELECT COUNT(*) AS c FROM foo AS f", 'foo f', ['c' => 'COUNT(*)']],
            ["SELECT CURRENT_TIMESTAMP, f.bar AS b FROM foo AS f", 'foo f', ['#CURRENT_TIMESTAMP', 'b' => 'bar']],
            ["SELECT CURRENT_TIMESTAMP, f.bar AS b FROM foo AS f", 'foo f', [ConnectionManager::raw('CURRENT_TIMESTAMP'), 'b' => 'bar']]
        ];
    }

    public function testReset()
    {
        $select = new Select();
        $select->setTableName('foo')->addColumns('bar, baz');
        $this->assertSame("SELECT bar, baz FROM foo", $select->toString());

        $select->resetColumns();
        $this->assertSame("SELECT foo.* FROM foo", $select->toString());
    }
}