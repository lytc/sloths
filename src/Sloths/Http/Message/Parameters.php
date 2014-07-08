<?php

namespace Sloths\Http\Message;

class Parameters implements \Countable, \JsonSerializable, \IteratorAggregate, \ArrayAccess
{
    /**
     * @var array
     */
    protected $params = [];

    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        $this->fromArray($params);
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->params;
    }

    /**
     * @param array $params
     * @param bool $reset
     * @return $this
     */
    public function fromArray(array $params, $reset = true)
    {
        if ($reset) {
            $this->params = [];
        }

        foreach ($params as $name => $value) {
            $this->set($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->params[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->params);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->has($name)? $this->params[$name] : null;
    }

    /**
     * @param string $name
     */
    public function remove($name)
    {
        unset($this->params[$name]);
    }

    /**
     * @param string $name
     * @param mixed $value
     */
    public function __set($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function __get($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * @param string $name
     */
    public function __unset($name)
    {
        $this->remove($name);
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->params);
    }

    /**
     * @return array
     */
    public function jsonSerialize()
    {
        return $this->toArray();
    }

    /**
     * @return \ArrayIterator|\Traversable
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->toArray());
    }

    /**
     * @param string $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function offsetSet($name, $value)
    {
        return $this->set($name, $value);
    }

    /**
     * @param string $name
     */
    public function offsetUnset($name)
    {
        return $this->remove($name);
    }
}