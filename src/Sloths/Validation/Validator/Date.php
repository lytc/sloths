<?php

namespace Sloths\Validation\Validator;

class Date extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must be a valid date';

    /**
     * @param mixed $input
     * @return bool
     */
    public function validate($input)
    {
        if (!$input) {
            return false;
        }

        if ($input instanceof \DateTime) {
            return true;
        }

        if (!is_string($input)) {
            return false;
        }

        return !!date_create($input);
    }
}