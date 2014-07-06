<?php

namespace Sloths\Authentication\Storage;

interface StorageInterface
{
    /**
     * @return bool
     */
    public function exists();

    /**
     * @return mixed
     */
    public function read();

    /**
     * @param $data
     * @return $this
     */
    public function write($data);

    /**
     * @return $this
     */
    public function clear();
}