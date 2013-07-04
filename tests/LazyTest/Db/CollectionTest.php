<?php

namespace LazyTest\Db;

use LazyTest\Db\Model\User;

/**
 * @covers Lazy\Db\Collection
 */
class CollectionTest extends TestCase
{
    public function testCount()
    {
        $this->assertCount(4, User::all());
    }

    public function testCountAll()
    {
        $users = User::all()->limit(2);
        $this->assertSame(4, $users->countAll());
    }

    public function testToArray()
    {
        $expected = array(
            array(
                'id' => 1,
                'name' => 'name1'
            ),
            array(
                'id' => 2,
                'name' => 'name2'
            )
        );

        $this->assertEquals($expected, User::all(array(1, 2))->select(array('id', 'name'))->toArray());
    }

    public function testGetModelInstanceByPrimaryKeyValue()
    {
        $users = User::all();
        $this->assertNull($users->get(9999));
        $this->assertInstanceOf('LazyTest\Db\Model\User', $users->get(1));
    }

    public function testIterator()
    {
        $users = User::all();
        $count = 0;

        foreach ($users as $user) {
            $this->assertInstanceOf('LazyTest\Db\Model\User', $user);
            $this->assertSame($user, $users->get($user->id()));
            $count++;
        }

        $this->assertSame($count, count($users));
    }

    public function testGetSqlSelect()
    {
        $users = User::all();
        $expected = "SELECT users.id, users.name FROM users";
        $this->assertSame($expected, $users->getSqlSelect()->toString());
    }

    public function testMethodColumn()
    {
        $users = User::all()->limit(2);
        $this->assertSame(array('1', '2'), $users->column());
        $this->assertSame(array('1', '2'), $users->column('id'));
        $this->assertSame(array('name1', 'name2'), $users->column(1));
        $this->assertSame(array('name1', 'name2'), $users->column('name'));
    }

    public function testMethodPair()
    {
        $users = User::all()->limit(2);
        $this->assertSame(array('1' => 'name1', '2' => 'name2'), $users->pair());
        $this->assertSame(array('1' => 'name1', '2' => 'name2'), $users->pair('id', 'name'));
    }

    public function testFallbackMethod()
    {
        $users = User::all()->where('id > 2')->order('id DESC')->limit(2);
        $expectedSql = "SELECT users.id, users.name FROM users WHERE (id > 2) ORDER BY id DESC LIMIT 2";
        $this->assertSame($expectedSql, $users->getSqlSelect()->toString());
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Call undefined method foo
     */
    public function testMethod__callUndefinedMethodShouldThrowAnException()
    {
        User::all()->foo();
    }
}