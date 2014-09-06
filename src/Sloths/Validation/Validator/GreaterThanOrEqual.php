<?php

namespace Sloths\Validation\Validator;

class GreaterThanOrEqual extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must be greater than or equal to :value';

    /**
     * @var
     */
    protected $value;

    /**
     * @param $value
     */
    public function __construct($value)
    {
        $this->value = (string) $value;
    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        return [
            'value' => $this->value
        ];
    }

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return $input >= $this->value;
    }
}