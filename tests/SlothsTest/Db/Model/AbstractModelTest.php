<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Connection;
use Sloths\Db\ConnectionManager;
use Sloths\Db\Model\AbstractModel;
use Sloths\Db\Model\Collection;
use Sloths\Db\Model\Relation\BelongsToSchema;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Model\AbstractModel
 */
class AbstractModelTest extends TestCase
{
    public function testGetTableName()
    {
        $model = $this->getMockForAbstractClass('Sloths\Db\Model\AbstractModel', [], 'UserRole');
        $this->assertSame('user_roles', $model->getTableName());

        $ref = new \ReflectionClass($model);
        $tableNameProperty = $ref->getProperty('tableName');
        $tableNameProperty->setAccessible(true);
        $tableNameProperty->setValue($model, 'foo');

        $this->assertSame('foo', $model->getTableName());
    }

    public function testGetAndHasColumns()
    {
        $model = $this->getMockForAbstractClass('Sloths\Db\Model\AbstractModel', [], 'UserRole');
        $ref = new \ReflectionClass($model);
        $columnsProperty = $ref->getProperty('columns');
        $columnsProperty->setAccessible(true);
        $columnsProperty->setValue($model, ['id' => 'id', 'name' => 'name']);

        $this->assertSame(['id', 'name'], $model->getColumns());

        $this->assertTrue($model->hasColumn('name'));
        $this->assertFalse($model->hasColumn('foo'));
    }

    public function testGetDefaultSelectColumns()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getColumnsSchema', 'getLazyLoadColumnTypes']);

        $model->expects($this->once())->method('getColumnsSchema')->willReturn([
            'id' => AbstractModel::INT,
            'name' => AbstractModel::VARCHAR,
            'content' => AbstractModel::TEXT
        ]);

        $model->expects($this->once())->method('getLazyLoadColumnTypes')->willReturn([AbstractModel::TEXT]);
        $this->assertSame(['id', 'name'], $model->getDefaultSelectColumns());
    }

    public function testSetData()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['set']);
        $model->expects($this->once())->method('set')->with('name', 'foo');
        $model->setData(['name' => 'foo']);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['set', 'applyDataChange']);
        $model->expects($this->once())->method('set')->with('id', 1);
        $model->expects($this->once())->method('applyDataChange');

        $model->setData(['id' => 1]);
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidDataTypeShouldThrowAnException()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['set']);
        $model->setData('foo');
    }

    public function testReload()
    {
        $row = ['id' => 1, 'name' => 'foo'];

        $select = $this->getMock('select', ['where', 'first']);
        $select->expects($this->once())->method('where')->with('id = 1');
        $select->expects($this->once())->method('first')->willReturn($row);

        $table = $this->getMock('table', ['select']);
        $table->expects($this->once())->method('select')->with('*')->willReturn($select);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['table', 'id', 'setData'], [], 'User');
        $model->expects($this->once())->method('table')->willReturn($table);

        $model->expects($this->once())->method('id')->willReturn(1);
        $model->expects($this->once())->method('setData')->with($row);

        $model->reload();
    }

    public function testLoadColumn()
    {
        $select = $this->getMock('select', ['where', 'first']);
        $select->expects($this->once())->method('where')->with("id = 1")->willReturnSelf();
        $select->expects($this->once())->method('first')->willReturn(['foo' => 'bar']);
        $table = $this->getMock('table', ['select']);
        $table->expects($this->once())->method('select')->with('foo')->willReturn($select);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['exists', 'id', 'hasColumn', 'table']);
        $model->expects($this->once())->method('hasColumn')->with('foo')->willReturn(true);
        $model->expects($this->once())->method('exists')->willReturn(true);
        $model->expects($this->once())->method('id')->willReturn(1);
        $model->expects($this->once())->method('table')->willReturn($table);

        $this->assertSame('bar', $model->loadColumn('foo'));

    }

    public function testLoadColumnWithParentCollection()
    {
        $models = [
            $model = $this->getMock(__NAMESPACE__ . '\MockModel', ['getParentCollection', 'table'], [['id' => 1]]),
            new MockModel(['id' => 2]),
            new MockModel(['id' => 3]),
        ];

        $collection = new Collection([], new MockModel());
        $ref = new \ReflectionClass($collection);
        $modelsProperty = $ref->getProperty('models');
        $modelsProperty->setAccessible(true);
        $modelsProperty->setValue($collection, $models);


        $model->expects($this->once())->method('getParentCollection')->willReturn($collection);

        $select = $this->getMock('select', ['where', 'all']);
        $select->expects($this->once())->method('where')->with("id IN (1, 2, 3)")->willReturnSelf();
        $select->expects($this->once())->method('all')->willReturn([
            ['id' => 1, 'title' => 'foo'],
            ['id' => 2, 'title' => 'bar'],
        ]);

        $table = $this->getMock('table', ['select']);
        $table->expects($this->once())->method('select')->with(['id', 'title'])->willReturn($select);

        $model->expects($this->once())->method('table')->willReturn($table);

        $this->assertSame('foo', $model->loadColumn('title'));
        $this->assertSame('bar', $collection->getAt(1)->get('title'));
        $this->assertNull($collection->getAt(2)->get('title'));
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testLoadColumnShouldThrowAnExceptionIfHasNoColumn()
    {
        $model = new MockModel();
        $model->loadColumn('foo');
    }

    /**
     * @expectedException \LogicException
     */
    public function testLoadColumnOnNonExistingModelShouldThrowAnException()
    {
        $model = new MockModel();
        $model->loadColumn('title');
    }

    public function testGetDataForSave()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getChangedData', 'getColumns']);
        $model->expects($this->once())->method('getChangedData')->willReturn(['id' => 1, 'name' => 'foo']);
        $model->expects($this->once())->method('getColumns')->willReturn(['id', 'name']);
        $this->assertSame(['name' => 'foo'], $model->getDataForSave());
    }

    public function testSaveInsert()
    {
        $connection = $this->getMock('Sloths\Db\Connection', ['getLastInsertId'], ['dsn']);
        $connection->expects($this->once())->method('getLastInsertId')->willReturn(1);
        $connectionManager = new ConnectionManager();
        $connectionManager->setWriteConnection($connection);

        $insert = $this->getMock('insert', ['run']);
        $insert->expects($this->once())->method('run');

        $table = $this->getMock('table', ['insert']);
        $table->expects($this->once())->method('insert')->with(['name' => 'foo'])->willReturn($insert);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getDataForSave', 'table']);
        $model->expects($this->once())->method('getDataForSave')->willReturn(['name' => 'foo']);
        $model->expects($this->once())->method('table')->willReturn($table);


        $model->setConnectionManager($connectionManager);

        $model->save();
        $this->assertSame(1, $model->id());
    }

    public function testSaveInsertWithTimestamp()
    {
        $connection = $this->getMock('Sloths\Db\Connection', ['getLastInsertId'], ['dsn']);
        $connection->expects($this->once())->method('getLastInsertId')->willReturn(1);
        $connectionManager = $this->getMock('Sloths\Db\ConnectionManager', ['now']);
        $connectionManager->expects($this->once())->method('now')->willReturn('now');
        $connectionManager->setWriteConnection($connection);

        $insert = $this->getMock('insert', ['run']);
        $insert->expects($this->once())->method('run');

        $table = $this->getMock('table', ['insert']);
        $table->expects($this->once())->method('insert')->with(['name' => 'foo', 'created_time' => 'now'])->willReturn($insert);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getDataForSave', 'table', 'hasColumn']);
        $model->expects($this->once())->method('hasColumn')->with('created_time')->willReturn(true);
        $model->expects($this->once())->method('getDataForSave')->willReturn(['name' => 'foo']);
        $model->expects($this->once())->method('table')->willReturn($table);


        $model->setConnectionManager($connectionManager);

        $model->save();
        $this->assertSame(1, $model->id());
    }

    public function testSaveUpdate()
    {
        $update = $this->getMock('update', ['where', 'run']);
        $update->expects($this->once())->method('where')->with("id = 1")->willReturnSelf();
        $update->expects($this->once())->method('run');

        $table = $this->getMock('table', ['update']);
        $table->expects($this->once())->method('update')->with(['name' => 'foo'])->willReturn($update);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['exists', 'id', 'getDataForSave', 'table']);
        $model->expects($this->once())->method('exists')->willReturn(true);
        $model->expects($this->once())->method('id')->willReturn(1);
        $model->expects($this->once())->method('getDataForSave')->willReturn(['name' => 'foo']);
        $model->expects($this->once())->method('table')->willReturn($table);

        $model->save();
    }

    public function testSaveUpdateWithTimestamp()
    {
        $update = $this->getMock('update', ['where', 'run']);
        $update->expects($this->once())->method('where')->with("id = 1")->willReturnSelf();
        $update->expects($this->once())->method('run');

        $table = $this->getMock('table', ['update']);
        $table->expects($this->once())->method('update')->with(['name' => 'foo', 'modified_time' => 'now'])->willReturn($update);

        $connectionManager = $this->getMock('Sloths\Db\ConnectionManager');
        $connectionManager->expects($this->once())->method('now')->willReturn('now');

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['exists', 'id', 'getDataForSave', 'table', 'hasColumn']);
        $model->expects($this->once())->method('exists')->willReturn(true);
        $model->expects($this->once())->method('id')->willReturn(1);
        $model->expects($this->once())->method('getDataForSave')->willReturn(['name' => 'foo']);
        $model->expects($this->once())->method('table')->willReturn($table);
        $model->expects($this->once())->method('hasColumn')->with('modified_time')->willReturn(true);

        $model->setConnectionManager($connectionManager);
        $model->save();
    }

    public function testSaveUpdateWithNoDataChanges()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['exists', 'getDataForSave', 'applyDataChange', 'touchParents']);
        $model->expects($this->once())->method('exists')->willReturn(true);
        $model->expects($this->once())->method('getDataForSave')->willReturn([]);
        $model->expects($this->never())->method('applyDataChange');
        $model->expects($this->never())->method('touchParents');

        $model->save();
    }

    public function testSaveWithForceAndHasNoDataChange()
    {
        $update = $this->getMock('update', ['where', 'run']);
        $update->expects($this->once())->method('where')->with("id = 1")->willReturnSelf();
        $update->expects($this->once())->method('run');

        $table = $this->getMock('table', ['update']);
        $table->expects($this->once())->method('update')->with(['id' => 1])->willReturn($update);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['table', 'getDataForSave', 'exists', 'id']);
        $model->expects($this->once())->method('table')->willReturn($table);
        $model->expects($this->once())->method('exists')->willReturn(true);
        $model->expects($this->once())->method('id')->willReturn(1);
        $model->expects($this->once())->method('getDataForSave')->willReturn([]);

        $ref = new \ReflectionClass($model);
        $timestampProperty = $ref->getProperty('timestamps');
        $timestampProperty->setAccessible(true);
        $timestampProperty->setValue($model, false);

        $model->save(true);
    }

    public function testTouch()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['save']);
        $model->expects($this->once())->method('save')->with(true);
        $model->touch();
    }

    public function testSaveShouldTouchParents()
    {
        $parentSchema = new BelongsToSchema('modelClassName', 'foreignKey', true);

        $parentModel = $this->getMock('parentModel', ['touch']);
        $parentModel->expects($this->once())->method('touch');

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['doInsert', 'getAllBelongsToSchema', 'getRelation']);
        $model->expects($this->once())->method('doInsert')->willReturn([true]);
        $model->expects($this->once())->method('getAllBelongsToSchema')->willReturn([$parentSchema]);
        $model->expects($this->once())->method('getRelation')->willReturn($parentModel);
        $model->expects($this->once())->method('getRelation')->willReturn($parentModel);

        $model->save();
    }

    public function testDelete()
    {
        $delete = $this->getMock('delete', ['where', 'run']);
        $delete->expects($this->once())->method('where')->with('id = 1')->willReturnSelf();
        $delete->expects($this->once())->method('run');

        $table = $this->getMock('table', ['delete']);
        $table->expects($this->once())->method('delete')->willReturn($delete);

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['table', 'id']);
        $model->expects($this->once())->method('table')->willReturn($table);
        $model->expects($this->once())->method('id')->willReturn(1);

        $model->delete();
    }

    public function testSetWithColumn()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['hasColumn']);
        $model->expects($this->once())->method('hasColumn')->with('column_name')->willReturn(true);
        $model->set('columnName', 'value');

        $this->assertSame(['column_name' => 'value'], $model->getChangedData());
    }

    public function testSetWithParent()
    {
        $parentModel = $this->getMock('parentModel', ['id']);
        $parentModel->expects($this->once())->method('id')->willReturn(1);
        $belongsToSchema = new BelongsToSchema(get_class($parentModel), 'parent_id');

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getBelongsToSchema']);
        $model->expects($this->once())->method('getBelongsToSchema')->with('Parent')->willReturn($belongsToSchema);
        $model->set('Parent', $parentModel);

        $this->assertSame($parentModel, $model->getRelation('Parent', true));
        $this->assertSame(['parent_id' => 1], $model->getChangedData());
    }

    public function testSetMixedData()
    {
        $model = $this->getMockForAbstractClass('Sloths\Db\Model\AbstractModel');
        $model->set('foo', 1);
        $this->assertSame(['foo' => 1], $model->getMixedData());
    }

    public function testGet()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getOriginalData', 'getChangedData', 'getMixedData']);
        $model->expects($this->atLeast(1))->method('getOriginalData')->willReturn(['foo' => 'bar']);
        $model->expects($this->atLeast(1))->method('getChangedData')->willReturn(['bar' => 'baz']);
        $model->expects($this->once())->method('getMixedData')->willReturn(['baz' => 'qux']);

        $this->assertSame('bar', $model->get('foo'));
        $this->assertSame('baz', $model->get('bar'));
        $this->assertSame('qux', $model->get('baz'));
    }

    public function testGetFromRelationRelation()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getRelation']);
        $model->expects($this->once())->method('getRelation')->with('foo', true)->willReturnCallback(function($name, $cache, &$success) {
            $success = true;
            return 'bar';
        });

        $this->assertSame('bar', $model->get('foo'));
    }

    public function testGetLazyLoadColumn()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['exists', 'hasColumn', 'loadColumn']);
        $model->expects($this->once())->method('exists')->willReturn(true);
        $model->expects($this->once())->method('hasColumn')->with('foo')->willReturn(true);
        $model->expects($this->once())->method('loadColumn')->with('foo')->willReturn('bar');

        $this->assertSame('bar', $model->get('foo'));
    }

    public function testGetRelation()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['hasBelongsTo', 'getBelongsTo']);
        $model->expects($this->once())->method('hasBelongsTo')->with('belongsTo')->willReturn(true);
        $model->expects($this->once())->method('getBelongsTo')->with('belongsTo')->willReturn('foo');
        $this->assertSame('foo', $model->getRelation('belongsTo'));

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['hasHasMany', 'getHasMany']);
        $model->expects($this->once())->method('hasHasMany')->with('hasMany')->willReturn(true);
        $model->expects($this->once())->method('getHasMany')->with('hasMany')->willReturn('bar');
        $this->assertSame('bar', $model->getRelation('hasMany'));

        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['hasHasOne', 'getHasOne']);
        $model->expects($this->once())->method('hasHasOne')->with('hasOne')->willReturn(true);
        $model->expects($this->once())->method('getHasOne')->with('hasOne')->willReturn('baz');
        $this->assertSame('baz', $model->getRelation('hasOne'));
    }

    public function test__get()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['get']);
        $model->expects($this->once())->method('get')->with('foo')->willReturn('bar');

        $this->assertSame('bar', $model->foo);
    }

    public function test__set()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['set']);
        $model->expects($this->once())->method('set')->with('foo')->willReturn('bar');
        $model->foo = 'bar';
    }

    public function test__call()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getRelation']);
        $model->expects($this->once())->method('getRelation')->with('Foo', false)->willReturnCallback(function($name, $cache, &$success) {
            $success = true;
            return 'bar';
        });
        $this->assertSame('bar', $model->Foo());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test__callShouldThrowAnExceptionIfHasNoRelation()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getRelation']);
        $model->expects($this->once())->method('getRelation')->with('Foo', false)->willReturnCallback(function($name, $cache, &$success) {
            $success = false;
        });
        $model->Foo();
    }

    public function testToArrayWithHiddenColumn()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getOriginalData', 'getChangedData', 'getHiddenColumns']);
        $model->expects($this->once())->method('getOriginalData')->willReturn(['foo' => 'bar', 'bar' => 'baz', 'password' => 'password']);
        $model->expects($this->once())->method('getChangedData')->willReturn(['bar' => 'qux']);
        $model->expects($this->once())->method('getHiddenColumns')->willReturn('password');

        $this->assertSame(['foo' => 'bar', 'bar' => 'qux'], $model->toArray());
    }

    public function testOnlyAndExcept()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['toArray']);
        $model->expects($this->exactly(2))->method('toArray')->willReturn(['foo' => 'bar', 'bar' => 'baz']);
        $this->assertSame(['foo' => 'bar'], $model->only('foo'));
        $this->assertSame(['bar' => 'baz'], $model->except('foo'));
    }

    public function testJsonSerialize()
    {
        $data = ['foo' => 'bar', 'bar' => 'baz'];
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['toArray']);
        $model->expects($this->once())->method('toArray')->willReturn($data);
        $this->assertSame(json_encode($data), json_encode($model));
    }

    public function testSerializable()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel', ['getOriginalData']);
        $model->expects($this->once())->method('getOriginalData')->willReturn(['foo' => 'bar']);

        $model = unserialize(serialize($model));
        $ref = new \ReflectionClass($model);
        $originalDataProperty = $ref->getProperty('data');
        $originalDataProperty->setAccessible(true);
        $this->assertSame(['foo' => 'bar'], $originalDataProperty->getValue($model));
    }

    /**
     * @dataProvider dataProviderTestAll
     */
    public function testAll(array $args, $expectedSql)
    {
        $connection = new Connection('dsn');
        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        MockModel::setDefaultConnectionManager($connectionManager);
        $posts = call_user_func_array(__NAMESPACE__ . '\MockModel::all', $args);

        $this->assertSame($expectedSql, $posts->getSqlSelect()->toString());
    }

    public function dataProviderTestAll()
    {
        return [
            [[], "SELECT posts.id, posts.title, posts.created_time, posts.modified_time FROM posts"],
            [[1], "SELECT posts.id, posts.title, posts.created_time, posts.modified_time FROM posts WHERE (id = 1)"],
            [[[1, 2]], "SELECT posts.id, posts.title, posts.created_time, posts.modified_time FROM posts WHERE (id IN (1, 2))"],
            [["name = 'foo'"], "SELECT posts.id, posts.title, posts.created_time, posts.modified_time FROM posts WHERE (name = 'foo')"],
            [['name', 'foo'], "SELECT posts.id, posts.title, posts.created_time, posts.modified_time FROM posts WHERE (name = 'foo')"],
            [['id > ?', 10], "SELECT posts.id, posts.title, posts.created_time, posts.modified_time FROM posts WHERE (id > 10)"],
            [[['name' => 'foo', 'id > ?' => 10]], "SELECT posts.id, posts.title, posts.created_time, posts.modified_time FROM posts WHERE (name = 'foo' AND id > 10)"],
        ];
    }

    public function testFirst()
    {
        $rows = [
            ['id' => 1, 'title' => 'foo']
        ];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')->with("SELECT posts.* FROM posts WHERE (id = 1) LIMIT 1")->willReturn($stmt);

        $connectionManager = new ConnectionManager();
        $connectionManager->setConnection($connection);

        MockModel::setDefaultConnectionManager($connectionManager);

        $model = MockModel::first(1);
        $this->assertSame($rows[0], $model->toArray());
    }

    public function testCreate()
    {
        $model = MockModel::create(['title' => 'foo', 'foo' => 'bar']);
        $this->assertInstanceOf(__NAMESPACE__ . '\MockModel', $model);
        $this->assertSame(['title' => 'foo', 'foo' => 'bar'], $model->toArray());
    }

    public function test__callStatic()
    {
        $collection = MockModel::all();
        $this->assertInstanceOf('Sloths\Db\Model\Collection', $collection);
        $this->assertInstanceOf(__NAMESPACE__ . '\MockModel', $collection->getModel());
    }

    /**
     * @expectedException \BadMethodCallException
     */
    public function test__callStaticShouldThrowAnExceptionIfHasNoStaticMethod()
    {
        MockModel::foo();
    }

    public function testGetNamespaceName()
    {
        $this->assertSame(__NAMESPACE__, (new MockModel())->getNamespaceName());
    }
}

class MockModel extends AbstractModel
{
    protected $tableName = 'posts';
    protected $columns = [
        'id' => self::INT,
        'title' => self::VARCHAR,
        'content' => self::TEXT,
        'created_time' => self::DATETIME,
        'modified_time' => self::DATETIME
    ];
}