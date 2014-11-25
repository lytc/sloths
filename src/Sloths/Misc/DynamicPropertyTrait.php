<?php

namespace Sloths\Misc;

trait DynamicPropertyTrait
{
    /**
     * @var array
     */
    protected $dynamicProperties = [];

    /**
     * @param array $properties
     * @return $this
     */
    public function addDynamicProperties(array $properties)
    {
        foreach ($properties as $name => $value) {
            $this->addDynamicProperty($name, $value);
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed $value
     * @return $this
     */
    public function addDynamicProperty($name, $value)
    {
        $this->dynamicProperties[$name] = $value;
        return $this;
    }

    /**
     * @param string $name
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function getDynamicProperty($name)
    {
        if (isset($this->dynamicProperties[$name])) {
            return $this->dynamicProperties[$name];
        }

        throw new \BadMethodCallException(sprintf('Call to undefined property %s::%s ', get_called_class(), $name));

    }

    /**
     * @param string $name
     * @return mixed
     * @throws \BadMethodCallException
     */
    public function __get($name)
    {
        return $this->getDynamicProperty($name);
    }
}