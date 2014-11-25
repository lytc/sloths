<?php

namespace Sloths\Misc;

class Parameters extends \ArrayObject
{
    /**
     * @param array $params
     */
    public function __construct(array $params = [])
    {
        parent::__construct([], self::ARRAY_AS_PROPS);
        $this->fromArray($params);
    }

    /**
     * @param string $name
     * @return mixed
     */
    public function offsetGet($name)
    {
        return $this->offsetExists($name)? parent::offsetGet($name) : null;
    }

    /**
     * @param array $params
     * @return $this
     */
    public function fromArray(array $params)
    {
        $this->exchangeArray($params);

        return $this;
    }

    /**
     * @param mixed $params
     * @return array|void
     */
    public function exchangeArray($params)
    {
        foreach ($params as $k => $v) {
            $this->set($k, $v);
        }
    }

    /**
     * @return array
     */
    public function toArray()
    {
        return $this->getArrayCopy();
    }

    /**
     * @return $this
     */
    public function reset()
    {
        parent::exchangeArray([]);
        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function set($name, $value)
    {
        $this->offsetSet($name, $value);
        return $this;
    }

    /**
     * @param string $name
     * @return bool
     */
    public function has($name)
    {
        return $this->offsetExists($name);
    }

    /**
     * @param string $name
     * @param mixed $default
     * @return mixed
     */
    public function get($name, $default = null)
    {
        return $this->offsetExists($name)? $this->offsetGet($name) : $default;
    }

    /**
     * @param string $name
     * @return $this
     */
    public function remove($name)
    {
        $this->offsetUnset($name);
        return $this;
    }

    /**
     * @return $this
     */
    public function trim()
    {
        foreach ($this as $k => $v) {
            if (is_string($v)) {
                $this[$k] = trim($v);
            }
        }

        return $this;
    }

    /**
     * @params string|array $args...
     * @return array
     */
    public function only()
    {
        $args = func_get_args();
        array_unshift($args, $this->toArray());
        $params = call_user_func_array('Sloths\Misc\ArrayUtils::only', $args);
        return new static($params);
    }

    /**
     * @params string|array $args...
     * @return array
     */
    public function except()
    {
        $args = func_get_args();
        array_unshift($args, $this->toArray());
        $params = call_user_func_array('Sloths\Misc\ArrayUtils::except', $args);

        return new static($params);
    }
}