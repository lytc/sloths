<?php

namespace Sloths\Validation\Rule;

use Sloths\Translation\Translator;
use Sloths\Validation\Rule;
use Sloths\Validation\ValidatableInterface;
use Sloths\Validation\Validator;

abstract class AbstractComposite implements ValidatableInterface, \Countable
{
    /**
     * @var array
     */
    protected $rules = [];

    /**
     * @var array
     */
    protected $failedRules = [];

    /**
     * @var Required
     */
    protected $required;

    /**
     * @var Translator
     */
    protected $translator;

    /**
     * @param array $rules
     */
    public function __construct($rules = null)
    {
        if ($rules) {
            $this->addRules($rules);
        }
    }

    /**
     * @return int
     */
    public function count()
    {
        return count($this->rules);
    }

    /**
     * @param Translator $translator
     * @return $this
     */
    public function setTranslator(Translator $translator)
    {
        $this->translator = $translator;

        foreach ($this->rules as $rule) {
            $rule->setTranslator($translator);
        }

        return $this;
    }

    /**
     * @return Translator
     */
    public function getTranslator()
    {
        return $this->translator;
    }

    /**
     * @return array
     */
    public function getRules()
    {
        return $this->rules;
    }

    /**
     * @return array
     */
    public function getFailedRules()
    {
        return $this->failedRules;
    }

    public function getMessages()
    {
        $messages = [];
        foreach ($this->getFailedRules() as $rule) {
            $messages[] = $rule->getMessage();
        }

        return $messages;
    }

    /**
     * addRules([$rule1, $rule2])
     * addRules('string|email')
     * addRules('string|length:3')
     * addRules(['string', 'length' => 3])
     *
     * @param array $rules
     * @return $this
     */
    public function addRules($rules)
    {
        if (is_string($rules)) {
            $rules = explode('|', $rules);
        }

        foreach ($rules as $name => $rule) {
            if (is_numeric($name)) {
                $this->add($rule);
            } else {
                $this->add($name, $rule);
            }
        }

        return $this;
    }

    /**
     * addRule($rule)
     * addRule('string')
     * addRule('string', [3])
     * addRule('string', 3)
     * addRule('string:3')
     *
     * @param mixed $rule
     * @param mixed $args
     * @return AbstractRule
     * @throws \InvalidArgumentException
     */
    public function add($rule, $args = [])
    {
        if (is_string($rule)) {
            $parts = explode(':', $rule, 2);
            if (2 == count($parts)) {
                $rule = $parts[0];
                $args = explode(',', $parts[1]);
            } else {
                if ($args && !is_array($args)) {
                    $args = [$args];
                }
            }

            $rule = Validator::createRule($rule, $args);
        }

        if (!$rule instanceof ValidatableInterface) {
            throw new \InvalidArgumentException(
                sprintf('Expects parameter 1 must be an instanceof %s\ValidatableInterface or string of valid rule, %s given',
                    __NAMESPACE__, is_object($rule)? 'instanceof ' . get_class($rule) : gettype($rule))
            );
        }

        if ($this->translator) {
            $rule->setTranslator($this->translator);
        }

        if ($rule instanceof Required) {
            $this->required = $rule;
        }

        $this->rules[] = $rule;

        return $this;
    }

    public function __call($name, $args)
    {
        $this->add(Validator::createRule($name, $args));
        return $this;
    }

    protected function validateRequired($input)
    {
        if ($this->required) {
            if (!$this->required->validate($input)) {
                $this->failedRules = [$this->required];
                return false;
            }
        } elseif ((new Blank())->validate($input)) {
            return true;
        }
    }
}