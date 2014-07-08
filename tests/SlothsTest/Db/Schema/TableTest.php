<?php

namespace SlothsTest\Db\Schema;

use Sloths\Db\Schema\Table;
use SlothsTest\Db\TestCase;

/**
 * @covers \Sloths\Db\Schema\Table
 */
class TableTest extends TestCase
{
    public function testGetName()
    {
        $table = new Table('foo', $this->mockConnection());
        $this->assertSame('foo', $table->getName());
    }

    public function testGetPrimaryKeyColumn()
    {
        $table = $this->getMock('Sloths\Db\Schema\Table', ['getColumns'], [], '', false);
        $table->expects($this->once())->method('getColumns')->willReturn([
            'name' => ['isPrimaryKey' => false],
            'id' => ['isPrimaryKey' => true],
        ]);

        $this->assertSame('id', $table->getPrimaryKeyColumn());
    }

    public function testGetDbName()
    {
        $connection = $this->mockConnection('getDbName');
        $connection->expects($this->once())->method('getDbName')->willReturn('foo');

        $table = new Table('bar', $connection);
        $this->assertSame('foo', $table->getDbName());
    }

    public function testGetColumns()
    {
        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->willReturn([
            ['Field' => 'id', 'Type' => 'int(11)', 'Key' => 'PRI', 'Extra' => 'auto_increment'],
            ['Field' => 'name', 'Type' => 'varchar(255)', 'Key' => null, 'Extra' => null]
        ]);

        $connection = $this->mockConnection('query');
        $connection->expects($this->once())->method('query')->willReturn($stmt);

        $table = new Table('foo', $connection);

        $expected = [
            'id' => ['name' => 'id', 'type' => 'int', 'isPrimaryKey' => true, 'isAutoIncrement' => true],
            'name' => ['name' => 'name', 'type' => 'varchar', 'isPrimaryKey' => false, 'isAutoIncrement' => false],
        ];

        $this->assertSame($expected, $table->getColumns());
    }

    public function testGetHasManyConstraints()
    {
        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->willReturn([
            ['TABLE_NAME' => 'posts', 'COLUMN_NAME' => 'author_id'],
            ['TABLE_NAME' => 'profiles', 'COLUMN_NAME' => 'user_id'],
        ]);

        $connection = $this->mockConnection('query');
        $connection->expects($this->once())->method('query')->willReturn($stmt);

        $postTable = $this->getMock('Sloths\Db\Schema\Table', ['getPrimaryKeyColumn'], ['posts', $connection]);
        $postTable->expects($this->once())->method('getPrimaryKeyColumn')->willReturn('id');

        $profileTable = $this->getMock('Sloths\Db\Schema\Table', ['getPrimaryKeyColumn'], ['profiles', $connection]);
        $profileTable->expects($this->once())->method('getPrimaryKeyColumn')->willReturn('user_id');

        $userTable = $this->getMock('Sloths\Db\Schema\Table', ['getDbName', '_fromCache'], ['users', $connection]);
        $userTable->expects($this->once())->method('getDbName')->willReturn('dbname');
        $userTable->expects($this->at(1))->method('_fromCache')->willReturn($postTable);
        $userTable->expects($this->at(2))->method('_fromCache')->willReturn($profileTable);

        $expected = [
            'posts' => ['table' => 'posts', 'foreignKey' => 'author_id']
        ];

        $this->assertSame($expected, $userTable->getHasManyConstraints());
    }

    public function testGetHasOneConstraints()
    {
        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->willReturn([
            ['TABLE_NAME' => 'posts', 'COLUMN_NAME' => 'author_id'],
            ['TABLE_NAME' => 'profiles', 'COLUMN_NAME' => 'user_id'],
        ]);

        $connection = $this->mockConnection('query');
        $connection->expects($this->once())->method('query')->willReturn($stmt);

        $postTable = $this->getMock('Sloths\Db\Schema\Table', ['getPrimaryKeyColumn'], ['posts', $connection]);
        $postTable->expects($this->once())->method('getPrimaryKeyColumn')->willReturn('id');

        $profileTable = $this->getMock('Sloths\Db\Schema\Table', ['getPrimaryKeyColumn'], ['profiles', $connection]);
        $profileTable->expects($this->once())->method('getPrimaryKeyColumn')->willReturn('user_id');

        $userTable = $this->getMock('Sloths\Db\Schema\Table', ['getDbName', '_fromCache'], ['users', $connection]);
        $userTable->expects($this->once())->method('getDbName')->willReturn('dbname');
        $userTable->expects($this->at(1))->method('_fromCache')->willReturn($postTable);
        $userTable->expects($this->at(2))->method('_fromCache')->willReturn($profileTable);

        $expected = [
            'profiles' => ['table' => 'profiles', 'foreignKey' => 'user_id']
        ];

        $this->assertSame($expected, $userTable->getHasOneConstraints());
    }

    public function testGetBelongsToConstraints()
    {
        $stmt = $this->getMock('PDOStatement', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->willReturn([
            ['COLUMN_NAME' => 'author_id', 'REFERENCED_TABLE_NAME' => 'users'],
            ['COLUMN_NAME' => 'category_id', 'REFERENCED_TABLE_NAME' => 'categories'],
        ]);

        $connection = $this->mockConnection('query');
        $connection->expects($this->once())->method('query')->willReturn($stmt);

        $posts = $this->getMock('Sloths\Db\Schema\Table', ['getDbName', '_fromCache'], ['posts', $connection]);
        $posts->expects($this->once())->method('getDbName')->willReturn('dbname');

        $expected = [
            'author_id' => ['table' => 'users', 'foreignKey' => 'author_id'],
            'category_id' => ['table' => 'categories', 'foreignKey' => 'category_id'],
        ];

        $this->assertSame($expected, $posts->getBelongsToConstraints());
    }

    public function testGetHasManyThroughConstraints()
    {
        $connection = $this->mockConnection();

        $postTable = $this->getMock('Sloths\Db\Schema\Table', ['getBelongsToConstraints'], ['posts', $connection]);
        $postTable->expects($this->once())->method('getBelongsToConstraints')->willReturn([
            'author_id' => ['table' => 'users', 'foreignKey' => 'author_id']
        ]);

        $userRoleTable = $this->getMock('Sloths\Db\Schema\Table', ['getBelongsToConstraints'], ['posts', $connection]);
        $userRoleTable->expects($this->once())->method('getBelongsToConstraints')->willReturn([
            'user_id' => ['table' => 'users', 'foreignKey' => 'user_id'],
            'role_id' => ['table' => 'roles', 'foreignKey' => 'role_id'],
        ]);

        $userTable = $this->getMock('Sloths\Db\Schema\Table', ['getHasManyConstraints', 'getBelongsToConstraints', '_fromCache'], ['users', $connection]);
        $userTable->expects($this->once())->method('getHasManyConstraints')->willReturn([
            'posts' => ['table' => 'posts', 'foreignKey' => 'author_id'],
            'user_roles' => ['table' => 'user_roles', 'foreignKey' => 'user_id'],
        ]);
        $userTable->expects($this->once())->method('getBelongsToConstraints')->willReturn([]);

        $userTable->expects($this->at(2))->method('_fromCache')->willReturn($postTable);
        $userTable->expects($this->at(3))->method('_fromCache')->willReturn($userRoleTable);

        $expected = [
            ['throughTableName' => 'user_roles', 'tableName' => 'roles', 'leftKey' => 'user_id', 'rightKey' => 'role_id']
        ];

        $this->assertSame($expected, $userTable->getHasManyThroughConstraints());
    }

    public function testFromCache()
    {
        $connection = $this->mockConnection();
        $table = Table::fromCache('foo', $connection);

        $this->assertSame($table, Table::fromCache('foo', $connection));
    }
}