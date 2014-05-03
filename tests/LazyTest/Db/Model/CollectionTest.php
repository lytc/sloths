<?php

namespace LazyTest\Db\Model;
use LazyTest\Db\Model\Stub\User;

class CollectionTest extends TestCase
{
    public function testInstance()
    {
        $users = User::all();
        $this->assertInstanceOf('Lazy\Db\Model\Collection', $users);
    }

    public function testGetSqlSelect()
    {
        $users = User::all();
        $sqlSelect = $users->getSqlSelect();
        $this->assertSame("SELECT users.id, users.name, users.password, users.created_time FROM users", $sqlSelect->toString());
    }

    public function testCount()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);
        $users = User::all();

        $connection->shouldReceive('selectAll')->with($users->getSqlSelect())->andReturn([[], []]);

        $this->assertCount(2, $users);
    }

    public function testFoundRows()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);
        $users = User::all();
        $users->calcFoundRows();

        $connection->shouldReceive('selectAllWithFoundRows')->once()->andReturn([
            'rows' => [[], []],
            'foundRows' => 4
        ]);

        $this->assertCount(2, $users);
        $this->assertSame(4, $users->foundRows());


    }

    public function testFoundRowsWithAlreadyFetched()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);

        $connection->shouldReceive('selectAll')->once()->andReturn([]);
        $connection->shouldReceive('selectAllWithFoundRows')->once()->andReturn([
            'rows' => [],
            'foundRows' => 4
        ]);
        $users = User::all();
        $users->toArray();
        $this->assertSame(4, $users->foundRows());
    }

    public function testFoundRowsWithoutCallCalcFoundRows()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);
        $users = User::all();

        $connection->shouldReceive('selectAllWithFoundRows')->once()->andReturn([
            'rows' => [],
            'foundRows' => 4
        ]);

        $this->assertSame(4, $users->foundRows());
    }

    public function testToArray()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1], ['id' => 2]];
        $connection->shouldReceive('selectAll')->once()->andReturn($data);
        $this->assertSame($data, $users->toArray());
    }

    public function testJsonEncode()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1], ['id' => 2]];
        $connection->shouldReceive('selectAll')->once()->andReturn($data);
        $this->assertSame(json_encode($data), $users->toJson());
        $this->assertSame(json_encode($data), json_encode($users));
    }

    public function testToArrayAndToJsonShouldNotIncludeHiddenColumn()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1, 'password' => 'password'], ['id' => 2, 'password' => 'password']];
        $connection->shouldReceive('selectAll')->once()->andReturn($data);

        $expected = [['id' => 1], ['id' => 2]];
        $this->assertSame($expected, $users->toArray());
        $this->assertSame(json_encode($expected), $users->toJson());
        $this->assertSame(json_encode($expected), json_encode($users));
    }

    public function testMethodColumn()
    {
        $connection = $this->mockConnection();
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar']];
        $connection->shouldReceive('selectAll')->once()->andReturn($data);

        $this->assertSame([1, 2], $users->column('id'));
        $this->assertSame(['foo', 'bar'], $users->column('name'));
        $this->assertSame([1 => 'foo', 2 => 'bar'], $users->column('name', 'id'));
    }

    public function testLazyLoading()
    {
        $stmt1 = $this->mock('PDOStatement');
        $stmt1->shouldReceive('fetchAll')->once()->andReturn([
            ['id' => 1],
            ['id' => 2]
        ]);

        $stmt2 = $this->mock('PDOStatement');
        $stmt2->shouldReceive('fetchAll')->once()->andReturn([
            ['id' => 1, 'profile' => 'foo'],
            ['id' => 2, 'profile' => 'bar'],
        ]);

        $pdo = $this->mockPdo();

        $pdo->shouldReceive('query')->once()->with("SELECT users.id, users.name, users.password, users.created_time FROM users")->andReturn($stmt1);
        $pdo->shouldReceive('query')->once()->with("SELECT users.id, users.profile FROM users WHERE (users.id IN(1, 2))")->andReturn($stmt2);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $users = User::all();
        foreach ($users as $user) {
            $user->profile;
        }
    }

    public function testEagerLoading()
    {
        $stmt1 = $this->mock('PDOStatement');
        $stmt1->shouldReceive('fetchAll')->once()->andReturn([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ]);

        $stmt2 = $this->mock('PDOStatement');
        $stmt2->shouldReceive('fetchAll')->once()->andReturn([
            ['id' => 4, 'created_user_id' => 1, 'name' => 'foo'],
            ['id' => 5, 'created_user_id' => 1, 'name' => 'bar'],
            ['id' => 6, 'created_user_id' => 2, 'name' => 'baz'],
        ]);

        $pdo = $this->mockPdo();

        $pdo->shouldReceive('query')->once()
            ->with("SELECT users.id, users.name, users.password, users.created_time FROM users")
            ->andReturn($stmt1);

        $pdo->shouldReceive('query')->once()
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id IN(1, 2, 3))")
            ->andReturn($stmt2);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $users = User::all();
        $expected = [['id' => 4, 'created_user_id' => 1, 'name' => 'foo'], ['id' => 5, 'created_user_id' => 1, 'name' => 'bar']];
        $this->assertSame($expected, $users[0]->Posts->toArray());

        $expected = [['id' => 6, 'created_user_id' => 2, 'name' => 'baz']];
        $this->assertSame($expected, $users[1]->Posts->toArray());

        $this->assertSame([], $users[2]->Posts->toArray());
    }

    public function testCompositeMethods()
    {
        $users = User::all();
        $user1 = $this->mock('LazyTest\Db\Model\Stub\User');
        $user1->shouldReceive('save')->once();
        $user1->shouldReceive('delete')->once();

        $user2 = $this->mock('LazyTest\Db\Model\Stub\User');
        $user2->shouldReceive('save')->once();
        $user2->shouldReceive('delete')->once();

        $user3 = $this->mock('LazyTest\Db\Model\Stub\User');
        $user3->shouldReceive('save')->once();
        $user3->shouldReceive('delete')->once();

        $users[0] = $user1;
        $users[1] = $user2;
        $users[2] = $user3;

        $users->save();
        $users->delete();
    }

    public function testMassSet()
    {
        $users = User::all();

        $users[0] = $user1 = new User();
        $users[1] = $user2 = new User();
        $users[2] = $user3 = new User();

        $users->name = 'foo';
        $this->assertSame('foo', $user1->name);
        $this->assertSame('foo', $user2->name);
        $this->assertSame('foo', $user3->name);
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function testCallUndefinedMethodShouldThrowAnException()
    {
        User::all()->badMethod();
    }
}