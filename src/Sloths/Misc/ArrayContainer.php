<?php

namespace Sloths\Misc;

class ArrayContainer implements \IteratorAggregate, \Countable, \JsonSerializable
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var bool
     */
    protected $recursive = false;

    /**
     * @param array $data
     * @param bool $recursive
     */
    public function __construct(array $data = [], $recursive = false)
    {
        $this->recursive = $recursive;
        $this->exchange($data);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->data);
    }

    /**
     * @return \Generator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->data);
    }

    /**
     * @param $data
     */
    public function exchange($data)
    {
        if ($this->recursive) {
            foreach ($data as $k => $v) {
                if (is_array($v)) {
                    $data[$k] = new static($v);
                }
            }
        }

        $this->data = $data;
    }

    /**
     * @return mixed
     */
    public function toArray()
    {
        $data = $this->data;

        foreach ($data as $k => $v) {
            if ($v instanceof self) {
                $data[$k] = $v->toArray();
            }
        }

        return $data;
    }

    /**
     * @param ArrayContainer $hash
     * @return $this
     */
    public function merge(ArrayContainer $hash)
    {
        $this->exchange(array_merge($this->toArray(), $hash->toArray()));
        return $this;
    }

    /**
     * @param ArrayContainer $hash
     * @return $this
     */
    public function replaceRecursive(ArrayContainer $hash)
    {
        $this->exchange(array_replace_recursive($this->toArray(), $hash->toArray()));
        return $this;
    }

    /**
     * @param $k
     * @param $v
     */
    public function set($k, $v)
    {
        if (is_array($v)) {
            $v = new static($v);
        }

        $this->data[$k] = $v;
    }

    /**
     * @param $k
     * @return null
     */
    public function get($k)
    {
        return isset($this->data[$k])? $this->data[$k] : null;
    }

    /**
     * @param $k
     * @param $v
     */
    public function __set($k, $v)
    {
        $this->set($k, $v);
    }

    /**
     * @param $k
     * @return null
     */
    public function __get($k)
    {
        return $this->get($k);
    }

    /**
     * @return mixed
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }
}