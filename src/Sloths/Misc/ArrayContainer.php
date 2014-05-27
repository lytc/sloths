<?php

namespace Sloths\Misc;

use Sloths\Util\ArrayUtils;

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
                    $data[$k] = new static($v, $this->recursive);
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
     * @param string $k
     * @param mixed $v
     */
    public function set($k, $v)
    {
        if (is_array($v)) {
            $v = new static($v, $this->recursive);
        }

        $this->data[$k] = $v;
    }

    /**
     * @param string $k
     * @return mixed
     */
    public function get($k)
    {
        return isset($this->data[$k])? $this->data[$k] : null;
    }

    /**
     * @param callable $callback
     * @return static
     */
    public function map(callable $callback)
    {
        return new static(array_map($callback, $this->toArray()), $this->recursive);
    }

    /**
     * @param string $characterMask
     * @return static
     */
    public function trim($characterMask = ' \t\n\r\0\x0B')
    {
        return $this->map(function($v) use ($characterMask) {
            return trim($v, $characterMask);
        });
    }

    /**
     * @param string|array $keys
     * @param mixed [$default]
     * @return static
     */
    public function only($keys, $default = null)
    {
        return new static(ArrayUtils::only($this->toArray(), $keys, $default), $this->recursive);
    }

    /**
     * @param string|array $keys
     * @return static
     */
    public function except($keys)
    {
        return new static(ArrayUtils::except($this->toArray(), $keys), $this->recursive);
    }

    /**
     * @param string $k
     * @param mixed $v
     */
    public function __set($k, $v)
    {
        $this->set($k, $v);
    }

    /**
     * @param string $k
     * @return mixed
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