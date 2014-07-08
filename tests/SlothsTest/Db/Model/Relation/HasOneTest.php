<?php

namespace Sloths\Db\Model\Relation;

use SlothsTest\Db\Model\Stub\User;
use SlothsTest\Db\Model\TestCase;

/**
 * @covers \Sloths\Db\Model\Model
 * @covers \Sloths\Db\Model\Relation\HasOne
 */
class HasOneTest extends TestCase
{
    public function testInstance()
    {
        $stmt = $this->getMock('PDOStatement', ['fetch']);
        $stmt->expects($this->once())->method('fetch')->with(\PDO::FETCH_ASSOC)->willReturn(['user_id' => 1, 'title' => 'foo']);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->once())->method('query')
            ->with("SELECT * FROM professors WHERE (user_id = 1) LIMIT 1")->willReturn($stmt);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $user = new User(['id' => 1]);
        $professor = $user->Professor;
        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\Professor', $professor);
        $this->assertSame('foo', $professor->title);
    }

    public function testEagerLoadingAndLazyLoading()
    {
        $users = User::all();

        $users[0] = $user1 = new User(['id' => 1], $users);
        $users[1] = $user2 = new User(['id' => 2], $users);
        $users[2] = $user3 = new User(['id' => 3], $users);

        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn([
            ['user_id' => 1, 'title' => 'foo'],
            ['user_id' => 3, 'title' => 'bar'],
        ]);

        $stmt2 = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt2->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn([
            ['user_id' => 1, 'resume' => 'baz'],
            ['user_id' => 3, 'resume' => 'qux'],
        ]);

        $pdo = $this->mockPdo('query');
        $pdo->expects($this->at(0))->method('query')
            ->with("SELECT professors.user_id, professors.title FROM professors WHERE (professors.user_id IN(1, 2, 3))")
            ->willReturn($stmt);

        $pdo->expects($this->at(1))->method('query')
            ->with("SELECT professors.user_id, professors.resume FROM professors WHERE (professors.user_id IN(1, 3))")
            ->willReturn($stmt2);

        $connection = $this->createConnection($pdo);
        User::setConnection($connection);

        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\Professor', $user1->Professor);
        $this->assertInstanceOf('SlothsTest\Db\Model\Stub\Professor', $user3->Professor);
        $this->assertNull($user2->Professor);

        $this->assertSame('foo', $user1->Professor->title);
        $this->assertSame('bar', $user3->Professor->title);

        $this->assertSame('baz', $user1->Professor->resume);
        $this->assertSame('qux', $user3->Professor->resume);
    }
}