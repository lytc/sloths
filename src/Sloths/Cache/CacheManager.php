<?php

namespace Sloths\Cache;

use Sloths\Cache\Storage\StorageInterface;

class CacheManager
{
    /**
     * @var StorageInterface
     */
    protected $storage;

    /**
     * @param StorageInterface $storage
     * @return $this
     */
    public function setStorage(StorageInterface $storage)
    {
        $this->storage = $storage;
        return $this;
    }

    /**
     * @param bool $strict
     * @return StorageInterface
     * @throws \RuntimeException
     */
    public function getStorage($strict = true)
    {
        if (!$this->storage && $strict) {
            throw new \RuntimeException('Storage is required');
        }

        return $this->storage;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        return $this->getStorage()->has($key);
    }

    /**
     * @param string $key
     * @return mixed
     */
    public function get($key)
    {
        return $this->getStorage()->get($key);
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return $this
     */
    public function set($key, $value, $expiration)
    {
        $this->getStorage()->set($key, $value, $expiration);
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove($key)
    {
        $this->getStorage()->remove($key);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeAll()
    {
        $this->getStorage()->removeAll();
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return $this
     */
    public function replace($key, $value, $expiration = 0)
    {
        $this->getStorage()->replace($key, $value, $expiration);
        return $this;
    }
}