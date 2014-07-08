<?php

namespace Sloths\Validation\Rule;

class Accepted extends AbstractRule
{
    /**
     *
     */
    const NULL_ON_FAILURE = FILTER_NULL_ON_FAILURE;

    /**
     * @var null
     */
    protected $flags;

    /**
     * @param null $flags
     */
    public function __construct($flags = null)
    {
        $this->flags = $flags;
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return false !== filter_var($input, FILTER_VALIDATE_BOOLEAN, $this->flags);
    }
}