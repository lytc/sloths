<?php

namespace Sloths\Validation\Rule;

abstract class AbstractExpectedRule extends AbstractRule
{
    /**
     * @var
     */
    protected $expected;

    /**
     * @param $expected
     */
    public function __construct($expected)
    {
        $this->expected = $expected;
        $this->validateExpected();

        if (!is_scalar($expected)) {
            $this->messageTemplateKey = 'NON_SCALAR';
        }
    }

    /**
     *
     */
    protected function validateExpected()
    {

    }

    /**
     * @return array
     */
    public function getDataForMessage()
    {
        if (is_string($this->expected) || is_numeric($this->expected)) {
            $result = $this->expected;
        } elseif (is_bool($this->expected)) {
            $result = $this->expected? 'TRUE' : 'FALSE';
        } else {
            $result = gettype($this->expected);
        }

        return [$result];
    }
}