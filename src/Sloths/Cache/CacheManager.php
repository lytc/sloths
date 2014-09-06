<?php

namespace Sloths\Cache;

use Sloths\Cache\Storage\StorageInterface;

class CacheManager
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    public function getStorage($strict = true)
    {
        if (!$this->storage && $strict) {
            throw new \RuntimeException('Storage is required');
        }

        return $this->storage;
    }

    public function has($key)
    {
        return $this->getStorage()->has($key);
    }

    public function get($key)
    {
        return $this->getStorage()->get($key);
    }

    public function set($key, $value, $expiration)
    {
        $this->getStorage()->set($key, $value, $expiration);
        return $this;
    }

    public function remove($key)
    {
        $this->getStorage()->remove($key);
        return $this;
    }

    public function removeAll()
    {
        $this->getStorage()->removeAll();
        return $this;
    }

    public function replace($key, $value)
    {
        $this->getStorage()->replace($key, $value);
        return $this;
    }
}