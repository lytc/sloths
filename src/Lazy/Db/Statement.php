<?php

namespace Lazy\Db;

use PDOStatement;

/**
 * Class Statement
 * @package Lazy\Db
 */
class Statement extends PDOStatement
{
    /**
     * @var array
     */
    protected static $logQueries = array();

    /**
     *
     */
    protected function __construct()
    {
        static::$logQueries[] = $this->queryString;
    }

    /**
     *
     */
    public static function clearQueryLog()
    {
        static::$logQueries = array();
    }

    /**
     * @return array
     */
    public static function getQueriesLog()
    {
        return static::$logQueries;
    }
}