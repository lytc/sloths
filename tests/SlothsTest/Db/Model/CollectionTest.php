<?php

namespace SlothsTest\Db\Model;
use SlothsTest\Db\Model\Stub\User;

/**
 * @covers \Sloths\Db\Model\Collection
 */
class CollectionTest extends TestCase
{
    public function testInstance()
    {
        $users = User::all();
        $this->assertInstanceOf('Sloths\Db\Model\Collection', $users);
    }

    public function testGetSqlSelect()
    {
        $users = User::all();
        $sqlSelect = $users->getSqlSelect();
        $this->assertSame("SELECT users.id, users.name, users.password, users.created_time FROM users", $sqlSelect->toString());
    }

    public function testCount()
    {
        $connection = $this->mockConnection('selectAll');
        User::setConnection($connection);
        $users = User::all();

        $connection->expects($this->once())->method('selectAll')->with($users->getSqlSelect())->willReturn([[], []]);

        $this->assertCount(2, $users);
    }

    public function testFoundRows()
    {
        $connection = $this->mockConnection('selectAllWithFoundRows');
        User::setConnection($connection);
        $users = User::all();
        $users->calcFoundRows();

        $connection->expects($this->once())->method('selectAllWithFoundRows')->willReturn([
            'rows' => [[], []],
            'foundRows' => 4
        ]);

        $this->assertCount(2, $users);
        $this->assertSame(4, $users->foundRows());


    }

    public function testFoundRowsWithAlreadyFetched()
    {
        $connection = $this->mockConnection('selectAll', 'selectAllWithFoundRows');
        User::setConnection($connection);

        $connection->expects($this->once())->method('selectAll')->willReturn([]);
        $connection->expects($this->once())->method('selectAllWithFoundRows')->willReturn([
            'rows' => [],
            'foundRows' => 4
        ]);
        $users = User::all();
        $users->toArray();
        $this->assertSame(4, $users->foundRows());
    }

    public function testFoundRowsWithoutCallCalcFoundRows()
    {
        $connection = $this->mockConnection('selectAllWithFoundRows');
        User::setConnection($connection);
        $users = User::all();

        $connection->expects($this->once())->method('selectAllWithFoundRows')->willReturn([
            'rows' => [],
            'foundRows' => 4
        ]);

        $this->assertSame(4, $users->foundRows());
    }

    public function testToArray()
    {
        $connection = $this->mockConnection('selectAll');
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1], ['id' => 2]];
        $connection->expects($this->once())->method('selectAll')->willReturn($data);
        $this->assertSame($data, $users->toArray());
    }

    public function testJsonEncode()
    {
        $connection = $this->mockConnection('selectAll');
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1], ['id' => 2]];
        $connection->expects($this->once())->method('selectAll')->willReturn($data);
        $this->assertSame(json_encode($data), $users->toJson());
        $this->assertSame(json_encode($data), json_encode($users));
    }

    public function testToArrayAndToJsonShouldNotIncludeHiddenColumn()
    {
        $connection = $this->mockConnection('selectAll');
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1, 'password' => 'password'], ['id' => 2, 'password' => 'password']];
        $connection->expects($this->once())->method('selectAll')->willReturn($data);

        $expected = [['id' => 1], ['id' => 2]];
        $this->assertSame($expected, $users->toArray());
        $this->assertSame(json_encode($expected), $users->toJson());
        $this->assertSame(json_encode($expected), json_encode($users));
    }

    public function testMethodColumn()
    {
        $connection = $this->mockConnection('selectAll');
        User::setConnection($connection);
        $users = User::all();

        $data = [['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar']];
        $connection->expects($this->once())->method('selectAll')->willReturn($data);

        $this->assertSame([1, 2], $users->column('id'));
        $this->assertSame(['foo', 'bar'], $users->column('name'));
        $this->assertSame([1 => 'foo', 2 => 'bar'], $users->column('name', 'id'));
    }

    public function testLazyLoading()
    {
        $stmt1 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->willReturn([
            ['id' => 1],
            ['id' => 2]
        ]);

        $stmt2 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->willReturn([
            ['id' => 1, 'profile' => 'foo'],
            ['id' => 2, 'profile' => 'bar'],
        ]);

        $pdo = $this->mockPdo('query');

        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT users.id, users.name, users.password, users.created_time FROM users")->willReturn($stmt1);
        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT users.id, users.profile FROM users WHERE (users.id IN(1, 2))")->willReturn($stmt2);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $users = User::all();
        foreach ($users as $user) {
            $user->profile;
        }
    }

    public function testEagerLoading()
    {
        $stmt1 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt1->expects($this->once())->method('fetchAll')->willReturn([
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ]);

        $stmt2 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->willReturn([
            ['id' => 4, 'created_user_id' => 1, 'name' => 'foo'],
            ['id' => 5, 'created_user_id' => 1, 'name' => 'bar'],
            ['id' => 6, 'created_user_id' => 2, 'name' => 'baz'],
        ]);

        $pdo = $this->mockPdo('query');

        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT users.id, users.name, users.password, users.created_time FROM users")
            ->willReturn($stmt1);

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT posts.id, posts.created_user_id, posts.modified_user_id, posts.name FROM posts WHERE (posts.created_user_id IN(1, 2, 3))")
            ->willReturn($stmt2);

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
        $user1 = $this->getMock('SlothsTest\Db\Model\Stub\User', ['save', 'delete']);
        $user1->expects($this->once())->method('save');
        $user1->expects($this->once())->method('delete');

        $user2 = $this->getMock('SlothsTest\Db\Model\Stub\User', ['save', 'delete']);
        $user2->expects($this->once())->method('save');
        $user2->expects($this->once())->method('delete');

        $user3 = $this->getMock('SlothsTest\Db\Model\Stub\User', ['save', 'delete']);
        $user3->expects($this->once())->method('save');
        $user3->expects($this->once())->method('delete');

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