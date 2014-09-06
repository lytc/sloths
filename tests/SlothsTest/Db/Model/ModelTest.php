<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Database;
use Sloths\Db\Model\AbstractModel;
use Sloths\Db\Model\Collection;
use SlothsTest\Db\Model\Stub\Post;
use SlothsTest\Db\Model\Stub\User;
use SlothsTest\Db\Model\Stub\Group;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Model\AbstractModel
 */
class ModelTest extends TestCase
{
    public function testGetTableName()
    {
        $this->assertSame('users', User::getTableName());
    }

    public function testFirst()
    {
        $row = [
            'id' => 1,
            'username' => 'admin',
            'password' => 'pass'
        ];

        $stmt = $this->getMock('stmt', ['fetch']);
        $stmt->expects($this->atLeast(1))->method('fetch')->with(\PDO::FETCH_ASSOC)->willReturn($row);

        $stmt2 = $this->getMock('stmt', ['fetch']);
        $stmt2->expects($this->once())->method('fetch')->with(\PDO::FETCH_ASSOC)->willReturn(false);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->at(0))
            ->method('query')
            ->with("SELECT users.* FROM users WHERE (users.id = 1) LIMIT 1")
            ->willReturn($stmt);

        $pdo->expects($this->at(1))
            ->method('query')
            ->with("SELECT users.* FROM users WHERE (username = 'admin') LIMIT 1")
            ->willReturn($stmt);

        $pdo->expects($this->at(2))
            ->method('query')
            ->with("SELECT users.* FROM users WHERE (username = 'foo') LIMIT 1")
            ->willReturn($stmt2);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);


        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $user = User::first(1);
        $this->assertSame($row, $user->toArray());

        $user = User::first('username = ?', 'admin');
        $this->assertSame($row, $user->toArray());

        $user = User::first('username = ?', ['foo']);
        $this->assertNull($user);
    }

    public function testAll()
    {
        $rows = [
            ['id' => 1],
            ['id' => 2],
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->atLeast(1))->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->at(0))
            ->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users WHERE (users.id IN (1, 2))")
            ->willReturn($stmt);

        $pdo->expects($this->at(1))
            ->method('query')
            ->with("SELECT users.id, users.username, users.password, users.created_time, users.modified_time FROM users WHERE (id < 3)")
            ->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $users = User::all([1, 2]);
        $this->assertSame($rows, $users->toArray());

        $users = User::all('id < ?', 3);
        $this->assertSame($rows, $users->toArray());
    }

    public function testCreate()
    {
        $data = ['username' => 'foo', 'password' => 'pass'];
        $user = User::create($data);
        $this->assertFalse($user->exists());
        $this->assertSame($data, $user->toArray());

        $data = ['id' => 1];
        $user = User::create($data);
        $this->assertTrue($user->exists());
        $this->assertSame(1, $user->id());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testFromArrayShouldThrowAnExceptionWithInvalidParam()
    {
        $user = new User();
        $user->fromArray('foo');
    }

    public function testReload()
    {
        $row = ['id' => 1, 'username' => 'username'];

        $stmt = $this->getMock('stmt', ['fetch']);
        $stmt->expects($this->once())->method('fetch')->with(\PDO::FETCH_ASSOC)->willReturn($row);

        $pdo = $this->getMock('mockpdo', ['query']);
        $pdo->expects($this->once())->method('query')->with("SELECT users.* FROM users WHERE (users.id = 1)")->willReturn($stmt);

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->once())->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1]);
        $user->reload();

        $this->assertSame('username', $user->username);
    }

    public function testGetDataForSave()
    {
        $user = new User(['username' => 'foo', 'password' => 'pass', 'foo' => 'foo']);
        $this->assertSame(['username' => 'foo', 'password' => 'pass'], $user->getDataForSave());

        $user = new User(['id' => 1, 'username' => 'foo', 'password' => 'pass']);
        $this->assertSame([], $user->getDataForSave());

        $user->username = 'bar';
        $this->assertSame(['username' => 'bar'], $user->getDataForSave());
    }

    public function testSaveInsert()
    {
        $now = '2014-08-29 12:04:03';
        $database = $this->getMock('Sloths\Db\Database', ['now']);
        $database->expects($this->any())->method('now')->willReturn($now);

        $pdo = $this->getMock('mockpdo', ['exec', 'lastInsertId']);
        $pdo->expects($this->once())->method('exec')->with("INSERT INTO users SET username = 'foo', created_time = '" . $now . "'");
        $pdo->expects($this->once())->method('lastInsertId')->willReturn(1);


        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['username' => 'foo']);
        $user->save();

        $this->assertSame(1, $user->id());
    }

    public function testSaveUpdate()
    {
        $now = '2014-08-29 12:04:03';
        $database = $this->getMock('Sloths\Db\Database', ['now']);
        $database->expects($this->any())->method('now')->willReturn($now);

        $pdo = $this->getMock('mockpdo', ['exec']);
        $pdo->expects($this->at(0))
            ->method('exec')
            ->with("UPDATE users SET username = 'foo', modified_time = '$now' WHERE (users.id = 1)");

        $pdo->expects($this->at(1))
            ->method('exec')
            ->with("UPDATE users SET username = 'bar', modified_time = '$now' WHERE (users.id = 1)");


        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1, 'username' => 'foo']);
        $user->save();
        $user->save(true);

        $user->username = 'bar';
        $user->save();

        $this->assertSame(1, $user->id());
    }

    public function testUpdateShouldDoNotingIfHaveNoDataChange()
    {
        $database = new Database();
        Post::setDatabase($database);

        $post = new Post(['id' => 1]);
        $post->save(true);
    }

    public function testTouch()
    {
        $now = '2014-08-29 12:04:03';
        $database = $this->getMock('Sloths\Db\Database', ['now']);
        $database->expects($this->any())->method('now')->willReturn($now);

        $pdo = $this->getMock('mockpdo', ['exec']);
        $pdo->expects($this->once())
            ->method('exec')
            ->with("UPDATE users SET modified_time = '{$now}' WHERE (users.id = 1)");

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1]);
        $user->touch();

        $this->assertSame($now, $user->modifiedTime);
    }

    public function testDelete()
    {
        $pdo = $this->getMock('mockpdo', ['exec']);
        $pdo->expects($this->once())
            ->method('exec')
            ->with("DELETE FROM users WHERE (users.id = 1)");

        $connection = $this->getMock('Sloths\Db\Connection', ['getPdo'], ['dsn']);
        $connection->expects($this->atLeast(1))->method('getPdo')->willReturn($pdo);

        $database = new Database();
        $database->setConnection($connection);

        User::setDatabase($database);

        $user = new User(['id' => 1]);
        $user->delete();
    }

    public function testGetAndSet()
    {
        $user = new User(['id' => 1, 'username' => 'foo']);
        $this->assertSame('foo', $user->get('username'));
        $this->assertSame('foo', $user->username);

        $user->set('username', 'bar');
        $this->assertSame('bar', $user->username);

        $user->username = 'baz';
        $this->assertSame('baz', $user->username);
    }

    public function testSetRelation()
    {
        $user = new User(['id' => 1]);
        $group = new Group(['id' => 2]);
        $user->Group = $group;

        $this->assertSame(2, $user->groupId);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallWithUndefinedMethodShouldThrowAnException()
    {
        $user = new User();
        $user->Foo();
    }

    public function testOnlyAndExcept()
    {
        $user = new User(['id' => 1, 'username' => 'foo', 'password' => 'pass', 'created_time' => 'createdtime']);
        $this->assertSame(['username' => 'foo', 'created_time' => 'createdtime'], $user->only('username created_time'));
        $this->assertSame(['username' => 'foo', 'created_time' => 'createdtime'], $user->except('id password'));
    }

    public function testJsonEncode()
    {
        $data = ['id' => 1, 'username' => 'foo', 'password' => 'pass', 'created_time' => 'createdtime'];
        $user = new User($data);
        $this->assertSame(json_encode($data), json_encode($user));
    }

    public function testToArrayWithRelationData()
    {
        $user = new User(['id' => 1, 'username' => 'foo']);
        $posts = new Collection([], __NAMESPACE__ . '\Stub\Post');
        $user->Posts = $posts;

        $expected = ['id' => 1, 'username' => 'foo', 'Posts' => $posts];
        $this->assertSame($expected, $user->toArray(true));
    }

    public function testToArrayWithHiddenColumn()
    {
        $model = new TestHiddenColumnModel(['id' => 1, 'username' => 'foo', 'password' => 'pass']);
        $this->assertSame(['id' => 1, 'username' => 'foo'], $model->toArray());
    }

    public function testTouchParentOnSave()
    {
        $user = $this->getMock('user', ['touch']);
        $user->expects($this->once())->method('touch');

        $post = $this->getMock(__NAMESPACE__ . '\Stub\Post', ['doInsert', 'getRelation']);
        $post->expects($this->once())->method('doInsert')->willReturn(true);
        $post->expects($this->once())->method('getRelation')->with('User')->willReturn($user);

        $post->save();
    }
}

class TestHiddenColumnModel extends AbstractModel
{
    protected static $columns = [
        'id' => self::INT,
        'username' => self::VARCHAR,
        'password' => self::VARCHAR
    ];

    protected static $hiddenColumns = ['password'];
}