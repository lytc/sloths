<?php

namespace LazyTest\Db;

use Lazy\Db\Statement;
use LazyTest\Db\Model\User;

/**
 * @covers Lazy\Db\AbstractModel
 */
class ModelInstanceTest extends TestCase
{
    public function test_get()
    {
        $user = User::first();
        $this->assertEquals(1, $user->id);
        $this->assertEquals(1, $user->id());
        $this->assertEquals('name1', $user->name);

        //@todo test camelCase
//        $this->assertNotEmpty($user->createdTime);
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Call undefined property foo
     */
    public function test__getShouldThrowAnExceptionIfHasNoDataBelongToModel()
    {
        $user = User::first();
        $user->foo;
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Trying to set undefined property foo
     */
    public function testSetColumnShouldThrowAnExceptionIfItHasNoAssociateColumn()
    {
        $user = User::first();
        $user->foo = 'bar';
    }

    /**
     * @todo
     */
    public function estSetColumnShouldSupportCamelCase()
    {
        $user = User::first();
        $user->createdTime = 'foo';
        $this->assertSame('foo', $user->createdTime);
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Can not set the auto column id
     */
    public function testSetColumnAutoShouldThrowAnException()
    {
        $user = User::first();
        $user->id = 1;
    }

    public function testMethodIsExists()
    {
        $user = new User(array('name' => 'name'));
        $this->assertFalse($user->isExists());

        $user = User::first();
        $this->assertTrue($user->isExists());
    }

    public function testToArray()
    {
        $user = User::first(1, 'id, name');
        $expected = array('id' => 1, 'name' => 'name1');
        $this->assertEquals($expected, $user->toArray());
    }

    public function testFromArray()
    {
        $user = new User();
        $user->fromArray(array('name' => 'name', 'foo' => 'foo'));
        $this->assertSame(array('name' => 'name'), $user->toArray());
    }

    public function testMethodIsDirty()
    {
        $user = new User();
        $this->assertFalse($user->isDirty());

        $user->name = 'name';
        $this->assertTrue($user->isDirty());

        $user = User::first(1);
        $this->assertFalse($user->isDirty());
        $user->name = 'name1';
        $this->assertFalse($user->isDirty());
        $user->name = 'name';
        $this->assertTrue($user->isDirty());
    }

    public function testMethodSaveShouldDoNothingIfNoDataChange()
    {
        $user = $this->getMock('LazyTest\Db\Model\User', array('beforeUpdate', 'afterUpdate'), array(array('id' => 1, 'name' => 'name1')));
        $user->expects($this->once())->method('beforeUpdate');
        $user->expects($this->never())->method('afterUpdate');

        $user->save();
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Trying to save an empty row
     */
    public function testMethodSaveInsertShouldThrowAnExceptionIfEmptyData()
    {
        $user = new User();
        $user->save();
    }

    public function testMethodSaveInsertShouldDoNothingIfBeforeInsertReturnFalse()
    {
        $user = $this->getMock('LazyTest\Db\Model\User', array('beforeInsert', 'afterInsert'), array(array('name' => 'name1')));
        $user->expects($this->once())->method('beforeInsert')->will($this->returnValue(false));
        $user->expects($this->never())->method('afterInsert');

        $user->save();
    }

    public function testMethodSaveUpdateShouldDoNothingIfBeforeUpdateReturnFalse()
    {
        $user = $this->getMock('LazyTest\Db\Model\User', array('beforeUpdate', 'afterUpdate'), array(array('id' => 1, 'name' => 'name1')));
        $user->expects($this->once())->method('beforeUpdate')->will($this->returnValue(false));
        $user->expects($this->never())->method('afterUpdate');

        $user->name = 'name';
        $user->save();
    }

    public function testMethodSaveShouldInsertANewRow()
    {
        $user = new User();
        $user->name = 'name';

        $connection = $this->getMockConnection(array('exec'));
        $connection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("INSERT INTO users (name) VALUES ('name')"));

        $prevConnection = $user->getConnection();
        $user->setConnection($connection);
        $user->save();
        $user->setConnection($prevConnection);
    }

    public function testMethodSaveShouldUpdateAnExistingRow()
    {
        $user = User::first(1);
        $user->name = 'name';

        $connection = $this->getMockConnection(array('exec'));
        $connection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("UPDATE users SET name = 'name' WHERE (id = '1')"));

        $prevConnection = $user->getConnection();
        $user->setConnection($connection);
        $user->save();
        $user->setConnection($prevConnection);
    }

    /**
     * @expectedException \Lazy\Db\Exception
     * @expectedExceptionMessage Trying to delete a non existing row
     */
    public function testMethodDeleteNonExistingRowShouldThrowAnException()
    {
        $user = new User();
        $user->delete();
    }

    public function testMethodDeleteShouldDeleteAnExistingRow()
    {
        $user = User::first(1);
        $user->name = 'name';

        $connection = $this->getMockConnection(array('exec'));
        $connection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("DELETE FROM users WHERE (id = '1')"));

        $prevConnection = $user->getConnection();
        $user->setConnection($connection);
        $user->delete();
        $user->setConnection($prevConnection);
    }

    public function testMethodReset()
    {
        $user = new User();
        $user->name = 'name1';
        $this->assertEquals(array('name' => 'name1'), $user->toArray());
        $user->reset();
        $this->assertSame(array(), $user->toArray());

        $user = User::first(1, 'id, name');
        $user->name = 'name';
        $this->assertEquals(array('id' => 1, 'name' => 'name'), $user->toArray());
        $user->reset();
        $this->assertEquals(array('id' => 1, 'name' => 'name1'), $user->toArray());
    }

    public function testMethodRefresh()
    {
        $user = User::first(1);

        Statement::clearQueryLog();
        $user->refresh();
        $this->assertSame(Statement::getQueriesLog(), array(
            "SELECT * FROM users WHERE (id = '1')"
        ));
    }

    public function testTransformValue()
    {
        $user = $this->getMock('LazyTest\Db\Model\User', array('setName', 'getName'));
        $user->expects($this->once())
            ->method('setName')
            ->with($this->equalTo('foo'))
            ->will($this->returnValue('bar'));

        $user->expects($this->exactly(3))
            ->method('getName')
            ->with($this->equalTo('bar'))
            ->will($this->returnValue('foo'));

        $user->fromArray(array('name' => 'foo'));
        $this->assertSame('foo', $user->name);
        $this->assertSame('foo', $user->name);
        $user->save();
        $user->refresh();
        $this->assertSame('foo', $user->name);
    }
}