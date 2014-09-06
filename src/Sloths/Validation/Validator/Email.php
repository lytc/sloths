<?php

namespace Sloths\Validation\Validator;

class Email extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must be a valid email address';

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        return false !== filter_var($input, FILTER_VALIDATE_EMAIL);
    }
}