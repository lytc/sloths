<?php

namespace Sloths\Validation\Rule;

class Url extends AbstractRule
{
    /**
     *
     */
    const PATH_REQUIRED = FILTER_FLAG_PATH_REQUIRED;
    /**
     *
     */
    const QUERY_REQUIRED = FILTER_FLAG_QUERY_REQUIRED;

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
        return false !== filter_var($input, FILTER_VALIDATE_URL, $this->flags);
    }
}