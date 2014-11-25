<?php

namespace SlothsTest\Db\Sql;

use Sloths\Db\Sql\AbstractSql;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Sql\AbstractSql
 */
class AbstractSqlTest extends TestCase
{
    public function testGetSpec()
    {
        $stubSql = new StubSql();
        $this->assertSame($stubSql->getSpec('Select'), $stubSql->getSpec('Select'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testGetUndefinedSpecShouldThrowAnException()
    {
        $stubSql = new StubSql();
        $stubSql->getSpec('foo');
    }

    public function test__call()
    {
        $selectSpec = $this->getMock('SelectSpec', ['setTable']);
        $selectSpec->expects($this->once())->method('setTable')->with('tableName');

        $sql = $this->getMock(__NAMESPACE__ . '\StubSql', ['getSpec']);
        $sql->expects($this->once())->method('getSpec')->with('Select')->willReturn($selectSpec);
        $sql->from('tableName');
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        $sql = new StubSql();
        $sql->foo();
    }

    public function testToString()
    {
        $spec1 = $this->getMock('Spec1', ['toString']);
        $spec1->expects($this->once())->method('toString')->willReturn('spec1');

        $spec3 = $this->getMock('Spec3', ['toString']);
        $spec3->expects($this->once())->method('toString')->willReturn('spec3');

        $stubSql = new StubSql();
        $ref = new \ReflectionClass($stubSql);
        $specsProp = $ref->getProperty('specs');
        $specsProp->setAccessible('public');
        $specsProp->setValue($stubSql, ['Spec1' => $spec1, 'Spec2' => null, 'Spec3' => $spec3]);

        $this->assertSame('spec1 spec3', $stubSql->toString());
    }
}

class StubSql extends AbstractSql
{
    protected $specs = [
        'Select' => null,
    ];

    protected $methods = [
        'from' => ['Select', 'setTable']
    ];
}