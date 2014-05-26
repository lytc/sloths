<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Model\Generator;

/**
 * @covers \Sloths\Db\Model\Generator
 */
class GeneratorTest extends TestCase
{
    protected $modelFile;
    protected $abstractModelFile;

    protected function getDataProvider()
    {
        return [
            'directory' => __DIR__ . '/_lazy_test_model_generator',
            'modelClassName' => 'ModelClassName',
            'abstractModelClassName' => 'AbstractModelClassName',
            'primaryKey' => 'primarykey',
            'tableName' => 'tablename',
            'columns' => ['id' => 'int', 'name' => 'varchar'],
            'hasMany' => ['Foo' => ['model' => 'Foo']],
            'hasOne' => ['Bar' => ['model' => 'Bar']],
            'belongsTo' =>  ['Baz' => ['model' => 'Baz']],
            'hasManyThrough' => ['Qux' => ['model' => 'Qux']]
        ];
    }

    protected function generate($newName = null)
    {
        $dataProvider = $this->getDataProvider();
        $generator = new Generator($dataProvider['modelClassName']);
        $generator->setDirectory($dataProvider['directory']);
        $generator->setAbstractModelClassName($dataProvider['abstractModelClassName']);
        $generator->setPrimaryKey($dataProvider['primaryKey']);
        $generator->setTableName($dataProvider['tableName']);
        $generator->setColumns($dataProvider['columns']);
        $generator->setHasMany($dataProvider['hasMany']);
        $generator->setHasOne($dataProvider['hasOne']);
        $generator->setBelongsTo($dataProvider['belongsTo']);
        $generator->setHasManyThrough($dataProvider['hasManyThrough']);

        if ($newName) {
            $generator->getClassGenerator()->setName($newName);
        }

        $generator->write();
        return $generator;
    }

    public function test()
    {
        $dataProvider = $this->getDataProvider();
        $modelClassName = $dataProvider['modelClassName'];
        $generator = $this->generate();

        $this->assertFileExists($generator->getFilename());
        $this->assertFileExists($generator->getAbstractModelFileName());

        require_once $generator->getAbstractModelFileName();
        require_once $generator->getFilename();

        $this->assertClassHasStaticAttribute('primaryKey', $modelClassName);
        $this->assertAttributeSame($dataProvider['primaryKey'], 'primaryKey', $modelClassName);

        $this->assertClassHasStaticAttribute('tableName', $modelClassName);
        $this->assertAttributeSame($dataProvider['tableName'], 'tableName', $modelClassName);

        $this->assertClassHasStaticAttribute('columns', $modelClassName);
        $this->assertAttributeSame($dataProvider['columns'], 'columns', $modelClassName);

        $this->assertClassHasStaticAttribute('hasMany', $modelClassName);
        $this->assertAttributeSame($dataProvider['hasMany'], 'hasMany', $modelClassName);

        $this->assertClassHasStaticAttribute('hasOne', $modelClassName);
        $this->assertAttributeSame($dataProvider['hasOne'], 'hasOne', $modelClassName);

        $this->assertClassHasStaticAttribute('belongsTo', $modelClassName);
        $this->assertAttributeSame($dataProvider['belongsTo'], 'belongsTo', $modelClassName);

        $this->assertClassHasStaticAttribute('hasManyThrough', $modelClassName);
        $this->assertAttributeSame($dataProvider['hasManyThrough'], 'hasManyThrough', $modelClassName);

        $newModelClassName = 'NewModelClassName';
        $generator2 = $this->generate($newModelClassName);

        require_once $generator2->getAbstractModelFileName();
        require_once $generator2->getFilename();

        $this->assertClassHasStaticAttribute('primaryKey', $newModelClassName);
        $this->assertAttributeSame($dataProvider['primaryKey'], 'primaryKey', $newModelClassName);

        $this->assertClassHasStaticAttribute('tableName', $newModelClassName);
        $this->assertAttributeSame($dataProvider['tableName'], 'tableName', $newModelClassName);

        $this->assertClassHasStaticAttribute('columns', $newModelClassName);
        $this->assertAttributeSame($dataProvider['columns'], 'columns', $newModelClassName);

        $this->assertClassHasStaticAttribute('hasMany', $newModelClassName);
        $this->assertAttributeSame($dataProvider['hasMany'], 'hasMany', $newModelClassName);

        $this->assertClassHasStaticAttribute('hasOne', $newModelClassName);
        $this->assertAttributeSame($dataProvider['hasOne'], 'hasOne', $newModelClassName);

        $this->assertClassHasStaticAttribute('belongsTo', $newModelClassName);
        $this->assertAttributeSame($dataProvider['belongsTo'], 'belongsTo', $newModelClassName);

        $this->assertClassHasStaticAttribute('hasManyThrough', $newModelClassName);
        $this->assertAttributeSame($dataProvider['hasManyThrough'], 'hasManyThrough', $newModelClassName);

        unlink($generator->getAbstractModelFileName());
        unlink($generator->getFilename());
        unlink($generator2->getFilename());
        rmdir(dirname($generator->getFilename()));
    }

    public function testNotGenerateAbstractModelClass()
    {
        $modelClassName = 'TestNotGenerateAbstractModelClass';
        $generator = new Generator($modelClassName);
        $generator->setDirectory(__DIR__ . '/_lazy_test_model_generator');
        $generator->write();

        require_once $generator->getFilename();

        $reflectionClass = new \ReflectionClass($modelClassName);
        $this->assertSame('Sloths\Db\Model\Model', $reflectionClass->getParentClass()->getName());

        unlink($generator->getFilename());
        rmdir(dirname($generator->getFilename()));
    }

    public function testFromTable()
    {
        $connection = $this->mockConnection();
        $table = $this->getMock('Sloths\Db\Schema\Table',
            ['getPrimaryKeyColumn', 'getName', 'getColumns', 'getHasManyConstraints', 'getHasOneConstraints', 'getBelongsToConstraints', 'getHasManyThroughConstraints'],
            ['users', $connection]);
        $table->expects($this->once())->method('getPrimaryKeyColumn')->willReturn('id');
        $table->expects($this->once())->method('getName')->willReturn('users');
        $table->expects($this->once())->method('getColumns')->willReturn([
            'id' => ['type' => 'int'],
            'name' => ['type' => 'varchar'],
        ]);
        $table->expects($this->once())->method('getHasManyConstraints')->willReturn([
            'posts' => ['table' => 'posts', 'foreignKey' => 'author_id']
        ]);
        $table->expects($this->once())->method('getHasOneConstraints')->willReturn([
            'profiles' => ['table' => 'profiles', 'foreignKey' => 'user_id']
        ]);
        $table->expects($this->once())->method('getBelongsToConstraints')->willReturn([
            'groups' => ['table' => 'groups', 'foreignKey' => 'group_id']
        ]);
        $table->expects($this->once())->method('getHasManyThroughConstraints')->willReturn([
            ['throughTableName' => 'user_roles', 'tableName' => 'roles', 'leftKey' => 'user_id', 'rightKey' => 'role_id']
        ]);

        $generator = Generator::fromTable($table, 'Model', $connection);
    }
}