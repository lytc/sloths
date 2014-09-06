<?php

namespace Sloths\Misc;

trait DynamicMethodTrait
{
    /**
     * @var array
     */
    protected $dynamicMethods = [];

    /**
     * @param array $methods
     * @return $this
     */
    public function addDynamicMethods(array $methods)
    {
        foreach ($methods as $name => $callback) {
            $this->addDynamicMethod($name, $callback);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param callable $callback
     * @return $this
     */
    public function addDynamicMethod($name, callable $callback)
    {
        $this->dynamicMethods[$name] = $callback;
        return $this;
    }

    /**
     * @param string $name
     * @param array $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function callDynamicMethod($name, array $args)
    {
        if (isset($this->dynamicMethods[$name])) {
            return call_user_func_array($this->dynamicMethods[$name], $args);
        }

        throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_called_class(), $name));
    }

    /**
     * @param $method
     * @param $args
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        return $this->callDynamicMethod($method, $args);
    }

}