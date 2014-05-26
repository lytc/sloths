<?php

namespace Sloths\View\Helper;

abstract class InputTag extends Tag
{
    /**
     * @var string
     */
    protected $name;

    /**
     * @var mixed
     */
    protected $value;

    /**
     * @param string $name
     * @return $this
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }

    /**
     * @param mixed $value
     * @return $this
     */
    public function setValue($value)
    {
        $this->value = $value;
        return $this;
    }

    /**
     * @param string [$name]
     * @param mixed [$value]
     * @param array [$attributes]
     * @return $this
     */
    public function inputTag($name = null, $value = null, array $attributes = [])
    {
        !$name || $this->setName($name);
        !null === $value || $this->setValue($value);
        !$attributes || $this->setAttributes($attributes);
        return $this;
    }

    /**
     * @return mixed
     */
    public function __invoke()
    {
        return call_user_func_array([$this, 'inputTag'], func_get_args());
    }

    /**
     * @return $this
     */
    protected function processName()
    {
        $this->attributes['name'] = $this->name;
        return $this;
    }

    /**
     * @return $this
     */
    protected function processValue()
    {
        if ($this->value) {
            $this->attributes['value'] = $this->value;
        }
        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        $this->processName();
        $this->processValue();

        return parent::render();
    }
}