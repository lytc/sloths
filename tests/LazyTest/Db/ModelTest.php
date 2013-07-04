<?php

namespace LazyTest\Db;

use LazyTest\Db\Model\Post;
use LazyTest\Db\Model\User;
use LazyTest\Db\Model\Permission;

/**
 * @covers Lazy\Db\AbstractModel
 */
class ModelTest extends TestCase
{
    public function testGetPrimaryKey()
    {
        $this->assertSame('id', User::getPrimaryKey());
    }

    public function testGetTableName()
    {
        $this->assertSame('users', User::getTableName());
    }

    public function testGetColumnSchema()
    {
        $expected = array(
            'id' => array(
                'type'  => 'int',
                'length' => 11,
                'unsigned' => true,
                'auto' => true
            ),
            'name' => array(
                'type' => 'varchar',
                'length' => 255
            ),
        );

        $this->assertSame($expected, User::getColumnSchema());
        $this->assertSame($expected['name'], User::getColumnSchema('name'));
    }

    public function testGetDefaultSelectColumns()
    {
        $expected = array('id', 'user_id', 'name');
        $this->assertSame($expected, Post::getDefaultSelectColumns());
    }

    public function testCreateSqlSelect()
    {
        $expected = "SELECT users.id, users.name FROM users";
        $this->assertSame($expected, User::createSqlSelect()->toString());

        $expected = "SELECT * FROM users";
        $this->assertSame($expected, User::createSqlSelect('*')->toString());
    }

    public function testCreateSqlInsert()
    {
        $expected = "INSERT INTO users (name) VALUES ('foo')";
        $this->assertSame($expected, User::createSqlInsert()->value(array('name' => 'foo'))->toString());
    }

    public function testCreateSqlDelete()
    {
        $expected = "DELETE FROM users WHERE (id = 1)";
        $this->assertSame($expected, User::createSqlDelete()->where("id = 1")->toString());
    }

    public function testCreateSqlUpdate()
    {
        $expected = "UPDATE users SET name = 'foo' WHERE (id = 1)";
        $this->assertSame($expected, User::createSqlUpdate()->data(array('name' => 'foo'))->where("id = 1")->toString());
    }

    public function testMethodFirstShouldReturnNullIfHasNoData()
    {
        $this->assertNull(User::first(9999));
    }

    public function testMethodFirstShouldReturnAnInstanceOfModel()
    {
        $this->assertInstanceOf('LazyTest\Db\Model\User', User::first(1));
    }

    public function testMethodFirstWithSelectColumn()
    {
        $connection = $this->getMockConnection(array('query'));
        $connection->expects($this->once())
            ->method('query')
            ->with($this->equalTo("SELECT users.id, users.name FROM users LIMIT 1"));

        $prevConnection = User::getConnection();
        User::setConnection($connection);
        User::first(null, array('id', 'name'));
        User::setConnection($prevConnection);
    }

    public function testMethodAllShouldReturnAnInstanceOfCollection()
    {
        $this->assertInstanceOf('Lazy\Db\Collection', User::all());
    }

    public function testMethodAllShouldSupportWhereConditionWithPrimaryKeyList()
    {
        $users = User::all(array(1, 2, 3));
        $expected = "SELECT users.id, users.name FROM users WHERE (id IN(1, 2, 3))";
        $this->assertSame($expected, $users->getSqlSelect()->toString());
    }

    public function testMethodCreate()
    {
        $this->assertInstanceOf('LazyTest\Db\Model\User', User::create());
    }

    public function testGetOneToManyAssociationSchema()
    {
        $expected = array(
            'Orders' => array(
                'model' => 'LazyTest\Db\Model\Order',
                'key' => 'user_id'
            ),
            'Posts' => array(
                'model' => 'LazyTest\Db\Model\Post',
                'key' => 'user_id'
            )
        );

        $this->assertSame($expected, User::getOneToManySchema());
        $this->assertSame($expected['Orders'], User::getOneToManySchema('Orders'));

        $this->assertNull(User::getOneToManySchema('UndefinedAssociation'));
    }

    public function testGetManyToOneAssociationSchema()
    {
        $expected = array(
            'User' => array(
                'model' => 'LazyTest\Db\Model\User',
                'key' => 'user_id'
            )
        );

        $this->assertSame($expected, Post::getManyToOneSchema());
        $this->assertSame($expected['User'], Post::getManyToOneSchema('User'));
    }

    public function testGetManyToManyAssociationSchema()
    {
        $expected = array(
            'Permissions' => array(
                'model' => 'LazyTest\Db\Model\Permission',
                'through' => 'LazyTest\Db\Model\UserPermission',
                'leftKey' => 'user_id',
                'rightKey' => 'permission_id'
            )
        );

        $this->assertSame($expected, User::getManyToManySchema());
        $this->assertSame($expected['Permissions'], User::getManyToManySchema('Permissions'));

        $expected = array(
            'Users' => array(
                'through' => 'LazyTest\Db\Model\UserPermission',
                'model' => 'LazyTest\Db\Model\User',
                'leftKey' => 'permission_id',
                'rightKey' => 'user_id'
            )
        );

        $this->assertSame($expected, Permission::getManyToManySchema());
        $this->assertSame($expected['Users'], Permission::getManyToManySchema('Users'));
    }

    public function testGetAssociationSchema()
    {
        $expected = array(
            'User' => array(
                'model' => 'LazyTest\Db\Model\User',
                'key' => 'user_id'
            )
        );

        $this->assertSame($expected, Post::getAssociationSchema());
        $this->assertSame($expected['User'], Post::getAssociationSchema('User'));
    }

    public function testGetLazyLoadColumns()
    {
        $this->assertSame(array(), User::getLazyLoadColumns());
    }

    public function testMethodRemove()
    {
        $connection = $this->getMockConnection(array('exec'));
        $connection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("DELETE FROM users WHERE (id IN(1, 2, 3))"));

        $prevConnection = User::getConnection();
        User::setConnection($connection);

        User::remove(array(1, 2, 3));

        User::setConnection($prevConnection);
    }

    public function testMethodInsert()
    {
        $connection = $this->getMockConnection(array('exec'));
        $connection->expects($this->once())
            ->method('exec')
            ->with($this->equalTo("INSERT INTO users (name) VALUES ('name1'), ('name2')"));

        $prevConnection = User::getConnection();
        User::setConnection($connection);

        User::insert(array(array('name' => 'name1'), array('name' => 'name2')));

        User::setConnection($prevConnection);
    }
}