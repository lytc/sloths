<?php

namespace Sloths\Cache\Storage;

class File implements StorageInterface
{
    /**
     * @var string
     */
    protected $directory;

    /**
     * @param string $directory
     * @return $this
     * @throws \InvalidArgumentException
     */
    public function setDirectory($directory)
    {
        if (!is_dir($directory)) {
            throw new \InvalidArgumentException('No such directory: ' . $directory);
        }

        $this->directory = $directory;
        return $this;
    }

    /**
     * @param bool $strict
     * @return string
     * @throws \RuntimeException
     */
    public function getDirectory($strict = true)
    {
        if (!$this->directory && $strict) {
            throw new \RuntimeException('Directory is required');
        }

        return $this->directory;
    }

    /**
     * @param string $key
     * @return bool
     */
    public function has($key)
    {
        // TODO: Implement has() method.
    }

    /**
     * @param $key
     * @param bool $success
     * @return mixed
     */
    public function get($key, &$success = null)
    {
        // TODO: Implement get() method.
    }

    /**
     * @param string $key
     * @param mixed $value
     * @param mixed $expiration
     * @return $this
     */
    public function set($key, $value, $expiration)
    {
        // TODO: Implement set() method.
    }

    /**
     * @param string $key
     * @return $this
     */
    public function remove($key)
    {
        // TODO: Implement remove() method.
    }

    /**
     * @return $this
     */
    public function removeAll()
    {
        // TODO: Implement removeAll() method.
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function replace($key, $value)
    {
        // TODO: Implement replace() method.
    }

}