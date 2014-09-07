<?php

namespace Sloths\Db\Table\Sql;

use Sloths\Cache\CacheableTrait;

class Select extends \Sloths\Db\Sql\Select
{
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
     * @return array
     */
    public function all()
    {
        $sql = $this->toString();

        if ($this->cacheExpiration) {
            $key = 'slothssqlselectcache.all.' . md5($sql);

            $result = $this->getCacheManager()->get($key, $success);

            if ($success) {
                return $result;
            }

            $result = $this->getConnection()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $this->getCacheManager()->set($key, $result, $this->cacheExpiration);

            return $result;
        }

        return $this->getConnection()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);

    }

    /**
     * @return mixed
     */
    public function first()
    {
        $this->limit(1);

        $sql = $this->toString();

        if ($this->cacheExpiration) {
            $key = 'slothssqlselectcache.one.' . md5($sql);

            $result = $this->getCacheManager()->get($key, $success);

            if ($success) {
                return $result;
            }

            $result = $this->getConnection()->query($sql)->fetchAll(\PDO::FETCH_ASSOC);
            $this->getCacheManager()->set($key, $result, $this->cacheExpiration);

            return $result;
        }

        return $this->getConnection()->query($sql)->fetch(\PDO::FETCH_ASSOC);
    }
}