<?php

namespace Sloths\Validation\Rule;

class Even extends AbstractRule
{
    /**
     * @var AllOf
     */
    protected $rule;

    /**
     *
     */
    public function __construct()
    {
        $this->rule = new AllOf([new Numeric(), new Divisible(2)]);
        $this->rule->required();
    }

    /**
     * @param $input
     * @return bool
     */
    public function validate($input)
    {
        return $this->rule->validate($input);
    }
}