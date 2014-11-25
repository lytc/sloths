<?php

namespace SlothsTest\Db\Model;

use Sloths\Db\Model\AbstractModel;
use Sloths\Db\Model\Collection;
use Sloths\Db\Sql\Select;
use SlothsTest\Db\Model\Stub\User;
use Sloths\Application\Service\ConnectionManager;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Model\Collection
 */
class CollectionTest extends TestCase
{
    protected $mockModel;

    public function setUp()
    {
        parent::setUp();
        $this->mockModel = $this->getMock('Sloths\Db\Model\AbstractModel');
    }

    public function testModel()
    {
        $collection = new Collection([], $this->mockModel);
        $this->assertSame($this->mockModel, $collection->getModel());
        $this->assertSame(get_class($this->mockModel), $collection->getModelClassName());
    }

    public function testLoad()
    {
        $select = $this->getMock('Sloths\Db\Table\Sql\Select', ['all']);
        $select->expects($this->exactly(2))->method('all')->willReturn([]);

        $collection = new Collection($select, $this->mockModel);

        $collection->load();
        $collection->load();
        $collection->load(true);
    }

    public function testReload()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['load'], [], '', false);
        $collection->expects($this->once())->method('load')->with(true);
        $collection->reload();
    }

    public function testGetAtAndFirst()
    {
        $rows = [
            ['id' => 1],
            ['id' => 2],
        ];

        $collection = new Collection($rows, $this->mockModel);
        $modelClassName = get_class($this->mockModel);

        $this->assertInstanceOf($modelClassName, $collection->getAt(0));
        $this->assertInstanceOf($modelClassName, $collection->getAt(1));
        $this->assertInstanceOf($modelClassName, $collection->first());
        $this->assertNull($collection->getAt(2));
    }

    public function testColumn()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['toArray'], [], '', false);
        $collection->expects($this->once())->method('toArray')->willReturn([['id' => 1, 'name' => 'foo'], ['id' => 2, 'name' => 'bar']]);
        $this->assertSame(['foo', 'bar'], $collection->column('name'));
    }

    public function testIds()
    {
        $model = $this->getMock('Sloths\Db\Model\AbstractModel');
        $model->expects($this->once())->method('getPrimaryKey')->willReturn('foo');

        $collection = $this->getMock('Sloths\Db\Model\Collection', ['column'], [[], $model]);
        $collection->expects($this->once())->method('column')->with('foo')->willReturn([1, 2]);
        $this->assertSame([1, 2], $collection->ids());
    }

    public function testToArrayAndCount()
    {
        $rows = [['id' => 1], ['id' => 2]];

        $collection = new Collection($rows, new MockModelForTestCollection());
        $this->assertSame($rows, $collection->toArray());
        $this->assertSame(2, $collection->count());
    }

    public function testJsonEncoding()
    {
        $rows = [['id' => 1], ['id' => 2]];

        $collection = new Collection($rows, new MockModelForTestCollection());
        $this->assertSame(json_encode($rows), json_encode($collection));
    }

    public function testTraversable()
    {
        $rows = [['id' => 1], ['id' => 2]];

        $collection = new Collection($rows, new MockModelForTestCollection());

        $count = 0;
        foreach ($collection as $model) {
            $this->assertInstanceOf(__NAMESPACE__ . '\MockModelForTestCollection', $model);
            $count++;
        }

        $this->assertSame(2, $count);
    }

    public function testFoundRows()
    {
        $select = $this->getMock('Sloths\Db\Table\Sql\Select', ['foundRows']);
        $select->expects($this->once())->method('foundRows')->willReturn(10);

        $collection = new Collection($select, $this->mockModel);
        $this->assertSame(10, $collection->foundRows());
    }

    public function testSet()
    {
        $users = new Collection([['id' => 1, 'username' => 'foo'], ['id' => 2, 'username' => 'bar']], new MockModelForTestCollection());
        $users->username = 'baz';

        $this->assertSame('baz', $users->getAt(0)->username);
        $this->assertSame('baz', $users->getAt(1)->username);
    }

    public function testSaveAndDelete()
    {
        $collection = $this->getMock('Sloths\Db\Model\Collection', ['callEach'], [], '', false);
        $collection->expects($this->at(0))->method('callEach')->with('save');
        $collection->expects($this->at(1))->method('callEach')->with('delete');

        $collection->save();
        $collection->delete();
    }

    public function testCallShouldPassToSelectIfMethodNotExists()
    {
        $select = $this->getMock('Sloths\Db\Table\Sql\Select', ['foo']);
        $select->expects($this->once())->method('foo');

        $collection = new Collection($select, $this->mockModel);
        $collection->foo();
    }
}

class MockModelForTestCollection extends AbstractModel
{
    protected $columns = [
        'id' => self::INT
    ];
}