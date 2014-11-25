<?php

namespace Sloths\Session;

class Container
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @param array $data
     */
    public function __construct(array &$data)
    {
        $this->data = &$data;
    }

    /**
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return isset($this->data[$name]);
    }

    /**
     * @param $name
     * @param $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->data[$name] = $value;
        return $this;
    }

    /**
     * @param $name
     * @return mixed|null
     */
    public function &get($name)
    {
        $result = null;
        if (!$this->has($name)) {
            return $result;
        }

        return $this->data[$name];
    }

    /**
     * @param $name
     * @return $this
     */
    public function remove($name)
    {
        unset($this->data[$name]);
        return $this;
    }
}