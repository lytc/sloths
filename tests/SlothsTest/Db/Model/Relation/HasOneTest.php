<?php

namespace SlothsTest\Db\Model\Relation;

use MockModel\User;
use Sloths\Db\ConnectionManager;
use Sloths\Db\Model\Collection;

class HasOneTest extends TestCase
{
    public function test()
    {
        $rows = [
            ['id' => 2, 'name' => 'foo']
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())
            ->method('query')->with("SELECT profiles.* FROM profiles WHERE (profiles.user_id = 1) LIMIT 1")->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        $user = new User(['id' => 1]);
        $user->setDefaultConnectionManager($connectionManager);

        $profile = $user->getHasOne('Profile');
        $this->assertSame($rows[0], $profile->toArray());

        $this->assertTrue($user->hasHasOne('Profile'));
    }

    public function testWithParentCollection()
    {
        $userRows = [
            ['id' => 1],
            ['id' => 2],
            ['id' => 3],
        ];

        $users = new Collection($userRows, new User());
        $user1 = $users->getAt(0);
        $user2 = $users->getAt(1);
        $user3 = $users->getAt(2);

        $profileRows = [
            ['user_id' => 1, 'resume' => 'foo'],
            ['user_id' => 2, 'resume' => 'bar'],
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($profileRows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())
            ->method('query')->with("SELECT profiles.* FROM profiles WHERE (profiles.user_id IN (1, 2, 3))")->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);
        $user1->setDefaultConnectionManager($connectionManager);

        $profile = $user1->getHasOne('Profile');
        $this->assertSame($profileRows[0], $profile->toArray());
        $this->assertSame($profileRows[1], $user2->getRelation('Profile', true)->toArray());
        $this->assertNull($user3->getRelation('Profile', true));
    }
}