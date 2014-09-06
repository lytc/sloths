<?php

namespace Sloths\Db\Sql;

use Sloths\Db\Connection;
use Sloths\Db\Database;

abstract class AbstractSql implements SqlInterface
{
    /**
     * @var \Sloths\Db\Sql\SqlInterface[]
     */
    protected $specs = [];

    /**
     * @var array
     */
    protected $methods = [];

    /**
     * @param $method
     * @param $args
     * @return $this
     * @throws \BadMethodCallException
     */
    public function __call($method, $args)
    {
        if (!isset($this->methods[$method])) {
            throw new \BadMethodCallException(sprintf('Call to undefined method %s::%s()', get_called_class(), $method));
        }

        $specMeta = $this->methods[$method];

        call_user_func_array([$this->getSpec($specMeta[0]), $specMeta[1]], $args);
        return $this;
    }

    /**
     * @param $name
     * @return SqlInterface
     * @throws \InvalidArgumentException
     */
    public function getSpec($name)
    {
        if (isset($this->specs[$name])) {
            return $this->specs[$name];
        }

        if (!array_key_exists($name, $this->specs)) {
            throw new \InvalidArgumentException('Unknown spec ' . $name);
        }

        $specClassName = __NAMESPACE__ . '\Spec\\' . $name;

        $this->specs[$name] = new $specClassName();
        return $this->specs[$name];
    }

    /**
     * @param string $name
     * @return bool
     */
    public function hasSpecInstance($name)
    {
        return isset($this->specs[$name]);
    }

    /**
     * @return string
     */
    public function toString()
    {
        $result = [];

        foreach ($this->specs as $spec) {
            if (!$spec) {
                continue;
            }

            if ($specStr = $spec->toString()) {
                $result[] = $specStr;
            }

        }

        return implode(' ', $result);
    }

    /**
     *
     */
    public function __clone()
    {
        foreach ($this->specs as $name => $instance) {
            if ($instance) {
                $this->specs[$name] = clone $instance;
            }
        }
    }
}