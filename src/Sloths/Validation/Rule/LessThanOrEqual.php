<?php

namespace Sloths\Validation\Rule;

class LessThanOrEqual extends AbstractExpectedRule
{
    /**
     * @var OneOf
     */
    protected $rule;

    /**
     * @param $expected
     */
    public function __construct($expected)
    {
        parent::__construct($expected);
        $this->rule = new OneOf([new Equals($expected), new LessThan($expected)]);
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