<?php

namespace Sloths\Session;

class Flash implements \IteratorAggregate, \ArrayAccess
{
    /**
     * @var array
     */
    protected $currentData;

    /**
     * @var array
     */
    protected $nextData = [];

    /**
     * @param string $name
     * @param Session $session
     */
    public function __construct($name = '__LAZY_FLASH_SESSION__', Session $session = null)
    {
        if (!$session) {
            $session = Session::getInstance();
        }

        $this->currentData = $session[$name]?: [];
        $session->getContainer()[$name] = &$this->nextData;
    }

    /**
     * @return array
     */
    public function getNextData()
    {
        return $this->nextData;
    }

    /**
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        return new \ArrayIterator($this->currentData);
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->currentData);
    }


    /**
     * @param string $name
     * @return mixed|null
     */
    public function get($name)
    {
        return $this->has($name)? $this->currentData[$name] : null;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        if (!$name) {
            $this->nextData[] = $value;
        } else {
            $this->nextData[$name] = $value;
        }
        return $this;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this->nextData[$name]);
        return $this;
    }

    /**
     * @return $this
     */
    public function clear()
    {
        $this->nextData = [];
        return $this;
    }

    /**
     * @return $this
     */
    public function keep()
    {
        $this->nextData = $this->currentData;
        return $this;
    }

    /**
     * @return $this
     */
    public function now()
    {
        $this->currentData = $this->nextData;
        return $this;
    }

    /**
     * @param string $name
     * @return null
     */
    public function __get($name)
    {
        return $this->get($name);
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
     */
    public function __unset($name)
    {
        $this->remove($name);
    }

    /**
     * @param mixed $name
     * @return bool
     */
    public function offsetExists($name)
    {
        return $this->has($name);
    }

    /**
     * @param mixed $name
     * @return null
     */
    public function offsetGet($name)
    {
        return $this->get($name);
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function offsetSet($name, $value)
    {
        $this->set($name, $value);
    }

    /**
     * @param mixed $name
     */
    public function offsetUnset($name)
    {
        $this->remove($name);
    }
}