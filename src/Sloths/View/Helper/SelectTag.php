<?php

namespace Sloths\View\Helper;

class SelectTag extends InputTag
{
    /**
     * @var string
     */
    protected $tagName = 'select';

    /**
     * @var array
     */
    protected $options = [];

    /**
     * @var string|callable
     */
    protected static $defaultValueProperty = 'id';

    /**
     * @var string|callable
     */
    protected static $defaultTextProperty = 'name';

    /**
     * @var string|callable
     */
    protected $valueProperty;

    /**
     * @var string|callable
     */
    protected $textProperty;

    /**
     * @param string|callable $property
     */
    public static function setDefaultValueProperty($property)
    {
        static::$defaultValueProperty = $property;
    }

    /**
     * @param string|callable $property
     */
    public static function setDefaultTextProperty($property)
    {
        static::$defaultTextProperty = $property;
    }

    /**
     * @param string|callable $property
     * @return $this
     */
    public function setValueProperty($property)
    {
        $this->valueProperty = $property;
        return $this;
    }

    /**
     * @return string|callable
     */
    public function getValueProperty()
    {
        return $this->valueProperty?: static::$defaultValueProperty;
    }

    /**
     * @param string|callable $property
     * @return $this
     */
    public function setTextProperty($property)
    {
        $this->textProperty = $property;
        return $this;
    }

    /**
     * @return string|callable
     */
    public function getTextProperty()
    {
        return $this->textProperty?: static::$defaultTextProperty;
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function setMultiple($state)
    {
        if ($state) {
            $this->setAttribute('multiple', 'multiple');
        } else {
            $this->removeAttribute('multiple');
        }

        return $this;
    }

    /**
     * @param string $name
     * @param mixed [$options]
     * @param mixed [$value]
     * @return mixed
     */
    public function selectTag($name, $options = null, $value = null)
    {
        !$options || $this->setOptions($options);
        return $this->__invoke($name, $value);
    }

    /**
     * @param mixed $value
     * @return array
     * @throws \InvalidArgumentException
     */
    protected function toArray($value)
    {
        if ($value instanceof \Traversable) {
            $value = iterator_to_array($value);
        } else if (is_object($value) && method_exists($value, 'toArray')) {
            $value = $value->toArray();
        }

        if (!is_array($value)) {
            throw new \InvalidArgumentException('Options expects an array or Traversable object');
        }

        return $value;
    }

    /**
     * @param mixed $options
     * @return $this
     */
    public function setOptions($options)
    {
        $this->options = $this->toArray($options);
        return $this;
    }

    /**
     * @param mixed $options
     * @return $this
     */
    public function appendOptions($options)
    {
        $options = $this->toArray($options);
        $this->options = array_replace($this->options, $options);
        return $this;
    }

    /**
     * @param mixed $options
     * @return $this
     */
    public function prependOptions($options)
    {
        $options = $this->toArray($options);
        $this->options = array_replace($options, $this->options);
        return $this;
    }

    /**
     * @param string $key
     * @param mixed $value
     * @return string
     */
    protected function buildOption($key, $value, array $selectedValues = [])
    {
        if (is_array($value)) {
            if (is_array(current($value))) {
                return $this->buildOptionGroup($key, $value);
            }

            $valueProperty = $this->getValueProperty();
            $textProperty = $this->getTextProperty();
            $val = null;
            $text = null;

            if (is_callable($valueProperty)) {
                $val = call_user_func($valueProperty, $key, $value);
            } elseif (isset($value[$valueProperty])) {
                $val = $value[$valueProperty];
            } else {
                $val = reset($value);
            }

            if (is_callable($textProperty)) {
                $text = call_user_func($textProperty, $key, $value);
            } elseif (isset($value[$textProperty])) {
                $text = $value[$textProperty];
            } else {
                reset($value);
                $text = next($value);
            }
        } else {
            $val = $key;
            $text = $value;
        }

        return sprintf('<option value="%s"%s>%s</option>', $this->escape($val), in_array($val, $selectedValues)? 'selected="selected"' : '', $this->escape($text));
    }

    /**
     * @param string $key
     * @param array $value
     * @return string
     */
    protected function buildOptionGroup($key, array $value)
    {
        $optGroup = [sprintf('<optgroup label="%s">', $this->escape($key))];
        foreach ($value as $val => $text) {
            $optGroup[] = $this->buildOption($val, $text);
        }

        $optGroup[] = '</optgroup>';

        return implode('', $optGroup);
    }

    /**
     * @return $this
     */
    public function processValue()
    {
        if ($this->options) {
            $values = $this->value? $this->value : [];
            is_array($values) || $values = [$values];
            $options = $this->options;

            $optionTags = [];

            foreach ($options as $key => $value) {
                $optionTags[] = $this->buildOption($key, $value, $values);
            }

            $this->setChildren($optionTags);
        }

        return $this;
    }
}