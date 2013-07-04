<?php

namespace Lazy\Db;

/**
 * Class Expr
 * @package Lazy\Db
 */
class Expr
{
    /**
     * @var
     */
    protected $expr;

    /**
     * @param string $expr
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
        return (String) $this->expr;
    }
}