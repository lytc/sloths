<?php

namespace Sloths\Validation\Validator;

class Alpha extends AbstractValidator
{
    /**
     * @var string
     */
    protected $defaultMessageTemplate = 'must contain only letters';

    /**
     * @var string
     */
    protected $pattern;

    /**
     * @param string $allows
     */
    public function __construct($allows = '\s')
    {
        $this->pattern = '/[^\p{L}' . $allows .']/u';
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        if (!is_string($input) && !is_numeric($input)) {
            return false;
        }

        return !preg_match($this->pattern, $input);
    }
}