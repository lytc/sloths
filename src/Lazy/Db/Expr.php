<?php

namespace Lazy\Db;

class Expr
{
    protected $expr;

    public function __construct($expr)
    {
        $this->expr = $expr;
    }

    public function toString()
    {
        return $this->expr;
    }
}