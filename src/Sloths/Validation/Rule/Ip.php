<?php

namespace Sloths\Validation\Rule;

class Ip extends AbstractRule
{
    /**
     *
     */
    const IPV4          = FILTER_FLAG_IPV4;
    /**
     *
     */
    const IPV6          = FILTER_FLAG_IPV6;
    /**
     *
     */
    const NO_PRIV_RANGE = FILTER_FLAG_NO_PRIV_RANGE;
    /**
     *
     */
    const NO_RES_RANGE  = FILTER_FLAG_NO_RES_RANGE;

    /**
     * @var null
     */
    protected $flags;

    /**
     * @param int $flags
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
        return false !== filter_var($input, FILTER_VALIDATE_IP, $this->flags);
    }
}