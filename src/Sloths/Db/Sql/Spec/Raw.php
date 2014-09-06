<?php

namespace Sloths\Db\Sql\Spec;

use Sloths\Db\Sql\SqlInterface;

class Raw implements SqlInterface
{
    /**
     * @var mixed
     */
    protected $expr;

    /**
     * @param mixed $expr
     */
    public function __construct($expr)
    {
        $this->expr = $expr;
    }

    /**
     * @return string
     */
    public function toString()
    {
        return (string) $this->expr;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }
}