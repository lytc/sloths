<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Model\ModelInterface;
use SlothsTest\TestCase;
use Sloths\Db\Model\Schema;

/**
 * @covers Sloths\Db\Model\Schema
 */
class SchemaTest extends TestCase
{
    public function testTransformTableNameToClassName()
    {
        $this->assertSame('User', Schema::transformTableNameToClassName('users'));
        $this->assertSame('UserRole', Schema::transformTableNameToClassName('user_roles'));
    }

    public function testTransformClassNameToTableName()
    {
        $this->assertSame('users', Schema::transformClassNameToTableName('User'));
        $this->assertSame('user_roles', Schema::transformClassNameToTableName('UserRole'));
    }

    public function testTransformColumnNameToPropertyName()
    {
        $this->assertSame('name', Schema::transformColumnNameToPropertyName('name'));
        $this->assertSame('createdTime', Schema::transformColumnNameToPropertyName('created_time'));
    }

    public function testTransformPropertyNameToColumnName()
    {
        $this->assertSame('name', Schema::transformPropertyNameToColumnName('name'));
        $this->assertSame('created_time', Schema::transformPropertyNameToColumnName('createdTime'));
    }

    public function testTransformToForeignKeyColumnName()
    {
        $this->assertSame('user_id', Schema::transformToForeignKeyColumnName('user'));
        $this->assertSame('user_id', Schema::transformToForeignKeyColumnName('users'));
    }

    public function test()
    {
        $columns = [
            'id' => ModelInterface::INT,
            'username' => ModelInterface::VARCHAR,
            'profile' => ModelInterface::TEXT
        ];

        $modelClassName = 'Model\User';
        $primaryKey = 'primary_key';
        $tableName = null;

        $hiddenColumns = [];
        $defaultLazyLoadColumnTypes = [ModelInterface::TEXT];
        $defaultSelectColumns = null;
        $hasOne = [
            'Profile',
            'Contact' => 'Contact'
        ];
        $belongsTo = [
            'Group',

        ];
        $hasMany = [
            'Posts',
            'Roles' => ['through' => 'UserRole']
        ];

        $schema = new Schema($modelClassName, $primaryKey, $tableName, $columns, $hiddenColumns,
            $defaultLazyLoadColumnTypes, $defaultSelectColumns,
            $hasOne, $belongsTo, $hasMany);

        $this->assertSame($primaryKey, $schema->getPrimaryKey());
        $this->assertSame('users', $schema->getTableName());
        $this->assertSame(array_keys($columns), $schema->getColumns());
        $this->assertTrue($schema->hasColumn('username'));
        $this->assertFalse($schema->hasColumn('foo'));
        $this->assertSame(ModelInterface::VARCHAR, $schema->getColumnType('username'));
        $this->assertSame(['id', 'username'], $schema->getDefaultSelectColumns());

        $this->assertSame("SELECT users.id, users.username FROM users", $schema->createSqlSelect()->toString());
        $this->assertSame("INSERT INTO users SET username = 'foo'", $schema->createSqlInsert()->values(['username' => 'foo'])->toString());
        $this->assertSame("UPDATE users SET username = 'foo'", $schema->createSqlUpdate()->values(['username' => 'foo'])->toString());
        $this->assertSame("DELETE FROM users", $schema->createSqlDelete()->toString());

        $this->assertSame(['model' => 'Model\Profile', 'foreignKey' => 'user_id', 'type' => Schema::HAS_ONE], $schema->getRelation('Profile'));
        $this->assertSame(['model' => 'Model\Contact', 'foreignKey' => 'user_id', 'type' => Schema::HAS_ONE], $schema->getRelation('Contact'));
        $this->assertSame(['model' => 'Model\Group', 'foreignKey' => 'group_id', 'type' => Schema::BELONGS_TO], $schema->getRelation('Group'));
        $this->assertSame(['model' => 'Model\Post', 'foreignKey' => 'user_id', 'type' => Schema::HAS_MANY], $schema->getRelation('Posts'));
        $this->assertSame(['through' => 'Model\UserRole', 'model' => 'Model\Role', 'type' => Schema::HAS_MANY],
            $schema->getRelation('Roles'));

        $this->assertTrue($schema->hasRelation('Profile'));
        $this->assertFalse($schema->hasRelation('Foo'));

        $this->assertTrue($schema->hasHasOneRelation('Profile'));
        $this->assertTrue($schema->hasBelongsToRelation('Group'));
        $this->assertTrue($schema->hasHasManyRelation('Roles'));

    }
}