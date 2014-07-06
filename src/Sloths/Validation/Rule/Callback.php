<?php

namespace Sloths\Validation\Rule;

class Callback extends AbstractRule
{
    /**
     * @var bool
     */
    protected $syntaxOnly = false;

    /**
     * @param bool $syntaxOnly
     */
    public function __construct($syntaxOnly = false)
    {
        $this->syntaxOnly = $syntaxOnly;
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return is_callable($input, $this->syntaxOnly);
    }
}