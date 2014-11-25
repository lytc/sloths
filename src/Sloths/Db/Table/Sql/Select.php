<?php

namespace Sloths\Db\Table\Sql;

use Sloths\Cache\CacheableTrait;

class Select extends \Sloths\Db\Sql\Select
{
    const CACHE_KEY_PREFIX = 'slothssqlselectcache';
    use SqlTrait;
    use CacheableTrait;

    /**
     * @var int
     */
    protected $cacheExpiration;

    /**
     * @param $expiration
     * @return $this
     */
    public function remember($expiration)
    {
        $this->cacheExpiration = $expiration;
        return $this;
    }

    /**
     * @return int
     */
    public function getCacheExpiration()
    {
        return $this->cacheExpiration;
    }

    /**
     * @return array
     */
    public function all()
    {
        $sql = $this->toString();

        if ($cacheExpiration = $this->getCacheExpiration() && $cacheManager = $this->getCacheManager(false)) {
            $key = static::CACHE_KEY_PREFIX . '.' . md5($sql);

            $result = $cacheManager->get($key, $success);

            if ($success) {
                return $result;
            }

            $result = $this->getConnection()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $cacheManager->set($key, $result, $cacheExpiration);

            return $result;
        }

        return $this->getConnection()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

    }

    /**
     * @return mixed
     */
    public function first()
    {
        $rows = $this->limit(1)->all();
        return reset($rows);
    }

    /**
     * @return int|mixed
     */
    public function foundRows()
    {
        $select = clone $this;

        if (!$select->hasSpecInstance('Having')) {
            $select->getSpec('Select')->resetColumns();
            $select->select('COUNT(*)');
        }

        $select->limit(null);

        $sql = $select->toString();

        if ($cacheExpiration = $this->getCacheExpiration() && $cacheManager = $this->getCacheManager(false)) {
            $key = static::CACHE_KEY_PREFIX . '.' . md5($sql);

            $result = $cacheManager->get($key, $success);

            if ($success) {
                return $result;
            }

            $result = (int) $select->getConnection()->query($sql)->fetchColumn();
            $cacheManager->set($key, $result, $cacheExpiration);

            return $result;
        }

        return (int) $select->getConnection()->query($sql)->fetchColumn();
    }
}