<?php

namespace Sloths\Cache\Storage;

interface StorageInterface
{
    /**
     * @param string $key
     * @return bool
     */
    public function has($key);

    /**
     * @param $key
     * @param bool $success
     * @return mixed
     */
    public function get($key, &$success = null);

    /**
     * @param string $key
     * @param mixed $value
     * @param mixed $expiration
     * @return $this
     */
    public function set($key, $value, $expiration);

    /**
     * @param string $key
     * @return $this
     */
    public function remove($key);

    /**
     * @return $this
     */
    public function removeAll();

    /**
     * @param string $key
     * @param mixed $value
     * @param int $expiration
     * @return bool
     */
    public function replace($key, $value, $expiration = 0);
}