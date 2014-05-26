<?php

namespace SlothsTest\Db\Model;

use SlothsTest\Db\Model\Stub\User;
use SlothsTest\Db\Model\Stub\FooBar;

/**
 * @covers \Sloths\Db\Model\Model
 */
class ModelTest extends TestCase
{
    public function testGetPrimaryKey()
    {
        $this->assertSame('id', User::getPrimaryKey());

        $this->assertSame('foo_id', FooBar::getPrimaryKey());
    }

    public function testGetTableName()
    {
        $this->assertSame('users', User::getTableName());
        $this->assertSame('foo_bar', FooBar::getTableName());
    }

    public function testExists()
    {
        $user = new User();
        $this->assertFalse($user->isExists());

        $user = new User(['id' => 1]);
        $this->assertTrue($user->isExists());
    }

    public function testMethodId()
    {
        $user = new User();
        $this->assertNull($user->id());

        $user = new User(['id' => 1]);
        $this->assertSame(1, $user->id());
    }

    public function testMethodGetAndSet()
    {
        $user = new User(['name' => 'foo', 'created_time' => 'bar']);

        $this->assertSame('foo', $user->get('name'));
        $this->assertSame('foo', $user->name);
        $this->assertSame('bar', $user->get('createdTime'));
        $this->assertSame('bar', $user->createdTime);

        $user->set('name', 'bar');
        $this->assertSame('bar', $user->name);

        $user->set('created_time', 'baz');
        $this->assertSame('baz', $user->get('createdTime'));

        $user->createdTime = 'qux';
        $this->assertSame('qux', $user->createdTime);
    }

    public function testGetNonLoadedColumn()
    {
        $stmt = $this->getMock('PDOStatement', ['fetchColumn']);
        $stmt->expects($this->once())->method('fetchColumn')->with(0)->willReturn('profile');

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->once())->method('query')->with("SELECT users.profile FROM users WHERE (users.id = 1)")->willReturn($stmt);
        $connection = $this->createConnection($pdo);

        User::setConnection($connection);
        $user = new User(['id' => 1]);
        $this->assertSame('profile', $user->profile);
    }

    public function testGetNonLoadedColumnOfNonExistingModelShouldReturnsNull()
    {
        $user = new User();
        $this->assertNull($user->profile);
    }

    public function testMethodFirst()
    {
        $connection = $this->mockConnection('select');
        $connection->expects($this->at(0))->method('select')->willReturn(['id' => 1]);
        $connection->expects($this->at(1))->method('select')->willReturn(null);

        User::setConnection($connection);
        $user = User::first(1);
        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\User', $user);

        $user = User::first(2);
        $this->assertNull($user);
    }

    public function testMethodAll()
    {
        $users = User::all();
        $this->assertInstanceOf('Sloths\Db\Model\Collection', $users);
    }

    public function testMethodCreate()
    {
        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\User', User::create());
    }

    public function testMethodSaveShouldCallInsert()
    {
        $user = $this->getMock('SlothsTest\Db\Model\Stub\User', ['insert']);
        $user->expects($this->once())->method('insert');
        $user->save();
    }

    public function testMethodSaveShouldCallUpdate()
    {
        $user = $this->getMock('SlothsTest\Db\Model\Stub\User', ['update'], [['id' => 1]]);
        $user->name = 'foo';
        $user->expects($this->once())->method('update');
        $user->save();
    }

    public function testMethodSaveInsertShouldUpdateExistingState()
    {
        $pdo = $this->mockPdo('exec', 'lastInsertId');
        $pdo->expects($this->once())->method('exec')->with("INSERT INTO users (name) VALUES ('foo')")->willReturn(true);
        $pdo->expects($this->once())->method('lastInsertId')->willReturn(1);
        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $user = new User(['name' => 'foo']);
        $user->save();
        $this->assertTrue($user->isExists());
        $this->assertSame(1, $user->id());
    }

    public function testMethodSaveUpdate()
    {
        $pdo = $this->mockPdo('exec');
        $pdo->expects($this->once())->method('exec')->with("UPDATE users SET name = 'foo' WHERE (id = 1)");
        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $user->name = 'foo';
        $user->save();
    }

    public function testMethodSaveShouldResetChangedData()
    {
        $user = $this->getMock('SlothsTest\Db\Model\Stub\User', ['insert'], [['name' => 'foo']]);
        $user->expects($this->once())->method('insert');

        $user->save();
        $this->assertSame([], $user->getChanged());
    }

    public function testMethodSaveUpdateShouldDoNothingIfHaveNoDataChange()
    {
        $user = $this->getMock('SlothsTest\Db\Model\Stub\User', ['update'], [['id' => 1, 'name' => 'foo']]);
        $user->expects($this->never())->method('update');

        $user->save();
    }

    public function testMethodDeleteShouldExecuteSqlDelete()
    {
        $pdo = $this->mockPdo('exec');
        $pdo->expects($this->once())->method('exec')->with("DELETE FROM users WHERE (id = 1)");
        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $user->delete();
    }

    /**
     * @expectedException \LogicException
     */
    public function testDeleteNonExistingModelShouldThrowAnException()
    {
        $user = new User();
        $user->delete();
    }

    public function testToArray()
    {
        $data = ['id' => 1, 'name' => 'foo'];
        $user = new User($data);
        $this->assertSame($data, $user->toArray());
    }

    public function testJsonSerialize()
    {
        $data = ['id' => 1, 'name' => 'foo'];
        $user = new User($data);
        $this->assertSame(json_encode($data), json_encode($user));
        $this->assertSame(json_encode($data), $user->toJson());
        $this->assertSame(json_encode($data), json_encode($user));
    }

    public function testToArrayAndJsonEncodeShouldNotIncludeHiddenColumn()
    {
        $data = ['id' => 1];
        $user = new User($data);

        $this->assertSame($data, $user->toArray());
        $this->assertSame(json_encode($data), $user->toJson());
        $this->assertSame(json_encode($data), json_encode($user));
    }

    public function testHiddenColumn()
    {
        $data = ['id' => 1, 'password' => 'foo'];
        $user = new User($data);

        $this->assertSame(['id' => 1], $user->toArray());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testCallUndefinedRelationShouldThrowAnException()
    {
        $user = new User(['id' => 1]);
        $user->Foo();
    }
}