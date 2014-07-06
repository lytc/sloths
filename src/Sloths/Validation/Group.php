<?php

namespace Sloths\Validation;

use Sloths\Translation\Translator;
use Sloths\Validation\Rule\AbstractComposite;
use Sloths\Validation\Rule\AllOf;
use Sloths\Misc\ArrayContainer;

class Group implements \Countable
{
    /**
     * @var array
     */
    protected $items = [];

    /**
     * @var array
     */
    protected $failedItems = [];

    /**
     * @var
     */
    protected $translator;

    /**
     * @param array $rules
     * @param Translator $translator
     */
    public function __construct(array $rules = [], Translator $translator = null)
    {
        foreach ($rules as $name => $rule) {
            $this->add($name, $rule);
        }

        if ($translator) {
            $this->setTranslator($translator);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->items);
    }

    /**
     * @param Translator $translator
     * @return $this
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;

        foreach ($this->items as $rule) {
            $rule->setTranslator($translator);
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @param $name
     * @param $rules
     * @return $this
     */
    public function add($name, $rules)
    {
        if (!$rules instanceof AbstractComposite) {
            $rules = new AllOf($rules);
        }

        if ($this->translator) {
            $rules->setTranslator($this->translator);
        }

        $this->items[$name] = $rules;
        return $this;
    }

    /**
     * @return array
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * @param $name
     * @return bool
     */
    public function has($name)
    {
        return array_key_exists($name, $this->items);
    }

    /**
     * @param $name
     * @return mixed
     */
    public function get($name)
    {
        return $this->has($name)? $this->items[$name] : null;
    }

    /**
     * @return array
     */
    public function getFailedItems()
    {
        return $this->failedItems;
    }

    /**
     * @return ArrayContainer
     */
    public function getMessages()
    {
        $rules = $this->getFailedItems();
        $result = [];

        foreach ($rules as $name => $rule) {
            $result[$name] = $rule->getMessages();
        }

        return new ArrayContainer($result);
    }

    /**
     * @param array $data
     * @return bool
     */
    public function validate(array $data)
    {
        $failedItems = [];

        foreach ($this->items as $name => $rule) {
            $value = isset($data[$name])? $data[$name] : null;
            if (!$rule->validate($value)) {
                $failedItems[$name] = $rule;
            }
        }

        $this->failedItems = $failedItems;
        return !$failedItems;
    }
}