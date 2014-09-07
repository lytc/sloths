<?php

namespace Sloths\Cache\Storage;

use Memcached as MemcachedResource;

class Memcached implements StorageInterface
{
    /**
     * @var MemcachedResource
     */
    protected $memcached;

    /**
     * @param \Memcached $memcached
     * @return $this
     */
    public function setMemcachedResource(MemcachedResource $memcached)
    {
        $this->memcached = $memcached;
        return $this;
    }

    /**
     * @param bool $strict
     * @return MemcachedResource
     * @throws \RuntimeException
     */
    public function getMemcachedResource($strict = true)
    {
        if (!$this->memcached && $strict) {
            throw new \RuntimeException('Memcached resource is required');
        }

        return $this->memcached;
    }

    /**
     * @param $key
     * @param bool $success
     * @return mixed
     */
    public function get($key, &$success = null)
    {
        $resource = $this->getMemcachedResource();
        $result = $resource->get($key);
        $success = $resource->getResultCode() === 0;

        return $result;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        $this->get($key, $success);
        return $success === true;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param mixed $expiration
     * @return $this
     */
    public function set($key, $value, $expiration)
    {
        $this->getMemcachedResource()->set($key, $value, $expiration);
        return $this;
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove($key)
    {
        $this->getMemcachedResource()->delete($key);
        return $this;
    }

    /**
     * @return $this
     */
    public function removeAll()
    {
        $this->getMemcachedResource()->flush();
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function replace($key, $value, $expiration = 0)
    {
        return $this->getMemcachedResource()->replace($key, $value, $expiration);
    }
}