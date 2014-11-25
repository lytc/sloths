<?php

namespace SlothsTest\Db\Table\Sql;
use Sloths\Db\Table\Sql\Select;
use SlothsTest\TestCase;

/**
 * @covers Sloths\Db\Table\Sql\Select
 */
class SelectTest extends TestCase
{
    public function testAll()
    {
        $rows = [['id' => 1]];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')->with("SELECT users.name FROM users")->willReturn($stmt);

        $select = $this->getMock('Sloths\Db\Table\Sql\Select', ['getConnection']);
        $select->expects($this->once())->method('getConnection')->willReturn($connection);

        $this->assertSame($rows, $select->table('users')->select('name')->all());
    }

    public function testAllWithCache()
    {
        $rows = [['id' => 1]];

        $cacheManager = $this->getMock('Sloths\Cache\CacheManager', ['get']);
        $cacheManager->expects($this->once())->method('get')
            ->with(Select::CACHE_KEY_PREFIX . '.' . md5("SELECT users.* FROM users"))
            ->willReturnCallback(function($key, &$success) use ($rows) {
                $success = true;
                return $rows;
            });

        $select = new Select();
        $select->setCacheManager($cacheManager);
        $select->table('users')->remember(10);

        $this->assertSame($rows, $select->all());
    }

    public function testAllWithCacheReturnsFalse()
    {
        $rows = [['id' => 1]];

        $stmt = $this->getMock('stmt', ['fetchAll']);
        $stmt->expects($this->once())->method('fetchAll')->with(\PDO::FETCH_ASSOC)->willReturn($rows);

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')->with("SELECT users.* FROM users")->willReturn($stmt);

        $select = $this->getMock('Sloths\Db\Table\Sql\Select', ['getConnection']);
        $select->expects($this->once())->method('getConnection')->willReturn($connection);

        $cacheKey = Select::CACHE_KEY_PREFIX . '.' . md5("SELECT users.* FROM users");

        $cacheManager = $this->getMock('Sloths\Cache\CacheManager', ['get', 'set']);
        $cacheManager->expects($this->once())->method('get')
            ->with($cacheKey)
            ->willReturnCallback(function($key, &$success) use ($rows) {
                $success = false;
            });

        $cacheManager->expects($this->once())->method('set')->with($cacheKey, $rows, 10);

        $select->setCacheManager($cacheManager);

        $this->assertSame($rows, $select->table('users')->remember(10)->all());
    }

    public function testFirst()
    {
        $select = $this->getMock('Sloths\Db\Table\Sql\Select', ['limit', 'all']);
        $select->expects($this->once())->method('limit')->with(1)->willReturnSelf();
        $select->expects($this->once())->method('all')->willReturn([['id' => 1]]);
        $this->assertSame(['id' => 1], $select->first());
    }

    public function testFoundRows()
    {
        $stmt = $this->getMock('stmt', ['fetchColumn']);
        $stmt->expects($this->once())->method('fetchColumn')->willReturn('10');

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')->with("SELECT COUNT(*) FROM users")->willReturn($stmt);

        $select = new Select();
        $select->setConnection($connection)->table('users');

        $this->assertSame(10, $select->foundRows());
    }

    public function testFoundRowsWithCache()
    {
        $cacheManager = $this->getMock('Sloths\Cache\CacheManager', ['get']);
        $cacheManager->expects($this->once())->method('get')
            ->with(Select::CACHE_KEY_PREFIX . '.' . md5("SELECT COUNT(*) FROM users"))
            ->willReturnCallback(function($key, &$success) {
                $success = true;
                return 10;
            });
        ;

        $select = new Select();
        $select->setCacheManager($cacheManager)
            ->table('users')->remember(10);

        $this->assertSame(10, $select->foundRows());
    }

    public function testFoundRowsWithCacheAndCacheReturnsFalse()
    {
        $stmt = $this->getMock('stmt', ['fetchColumn']);
        $stmt->expects($this->once())->method('fetchColumn')->willReturn('10');

        $connection = $this->getMock('Sloths\Db\Connection', ['query'], ['dsn']);
        $connection->expects($this->once())->method('query')->with("SELECT COUNT(*) FROM users")->willReturn($stmt);

        $cacheKey = Select::CACHE_KEY_PREFIX . '.' . md5("SELECT COUNT(*) FROM users");

        $cacheManager = $this->getMock('Sloths\Cache\CacheManager', ['get', 'set']);
        $cacheManager->expects($this->once())->method('get')
            ->with($cacheKey)
            ->willReturnCallback(function($key, &$success) {
                $success = false;
            });
        ;

        $cacheManager->expects($this->once())->method('set')->with($cacheKey, 10);

        $select = new Select();
        $select->setConnection($connection)->setCacheManager($cacheManager)
            ->table('users')->remember(10);

        $this->assertSame(10, $select->foundRows());
    }
}