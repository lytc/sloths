<?php

namespace Sloths\Cache;

trait CacheableTrait
{
    /**
     * @var
     */
    protected $cacheManager;

    /**
     * @param CacheManager $cacheManager
     * @return $this
     */
    public function setCacheManager(CacheManager $cacheManager)
    {
        $this->cacheManager = $cacheManager;
        return $this;
    }

    /**
     * @param bool $strict
     * @return CacheManager
     * @throws \RuntimeException
     */
    public function getCacheManager($strict = true)
    {
        if (!$this->cacheManager && $strict) {
            throw new \RuntimeException('A cache manager is required');
        }

        return $this->cacheManager;
    }
}