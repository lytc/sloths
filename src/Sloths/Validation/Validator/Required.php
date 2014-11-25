<?php

namespace Sloths\Validation\Validator;

class Required extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'is required';

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return $input !== '' && $input !== null;
    }
}