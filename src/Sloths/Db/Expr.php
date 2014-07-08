<?php

namespace Sloths\Db;

class Expr
{
    protected $expr;

    public function __construct($expr)
    {
        $this->expr = $expr;
    }

    public function getValue()
    {
        return $this->expr;
    }

    public function __toString()
    {
        return (string) $this->expr;
    }
}