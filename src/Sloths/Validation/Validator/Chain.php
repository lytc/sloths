<?php

namespace Sloths\Validation\Validator;

use Sloths\Validation\Validator;

class Chain
{
    public static function fromArray(array $rules)
    {
        foreach ($rules as $name => &$rule) {
            if ($rule instanceof ValidatorInterface) {
                continue;
            }

            if (is_string($name)) {
                $args = $rule;
            } else {
                $name = $rule;
                $args = [];
            }

            if (!is_array($args)) {
                $args = [$args];
            }

            $rule = Validator::createRule($name, $args);
        }

        return new static($rules);
    }

    /**
     * @var ValidatorInterface[]
     */
    protected $validators = [];

    /**
     * @var bool
     */
    protected $required = false;

    /**
     * @param ValidatorInterface[] $validators
     */
    public function __construct(array $validators = [])
    {
        $this->addValidators($validators);
    }

    /**
     * @param bool $state
     * @return $this
     */
    public function required($state = true)
    {
        $this->required = $state;
        return $this;
    }

    /**
     * @return bool
     */
    public function isRequired()
    {
        return !!$this->required;
    }

    /**
     * @param ValidatorInterface[] $validators
     */
    public function addValidators(array $validators)
    {
        foreach ($validators as $validator) {
            $this->add($validator);
        }
    }

    /**
     * @param ValidatorInterface $validator
     * @return $this
     */
    public function add(ValidatorInterface $validator)
    {
        if ($validator instanceof Required) {
            $this->required();
            return $this;
        }

        $this->validators[] = $validator;
        return $this;
    }

    /**
     * @return ValidatorInterface[]
     */
    public function getValidators()
    {
        return $this->validators;
    }

    /**
     * @param $input
     * @return bool|ValidatorInterface
     */
    public function validate($input)
    {
        $requiredValidator = new Required();

        if ($this->required) {
            if (!$requiredValidator->validate($input)) {
                return $requiredValidator;
            }
        } elseif (!$requiredValidator->validate($input)) {
            return true;
        }

        foreach ($this->validators as $validator) {
            if (!$validator->validate($input)) {
                return $validator;
            }
        }

        return true;
    }
}