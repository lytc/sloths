<?php

namespace Lazy\Db\Sql;

/**
 * Class Limit
 * @package Lazy\Db\Sql
 */
class Limit
{
    /**
     * @var int
     */
    protected $limit;

    /**
     * @param int $limit optional
     */
    public function __construct($limit = null)
    {
        if ($limit) {
            $this->limit($limit);
        }
    }

    /**
     * @param int $limit optional
     * @return $this
     */
    public function limit($limit = null)
    {
        if (!$limit) {
            return $this->limit;
        }

        $this->limit = (int) $limit;

        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->limit = null;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->limit) {
            return '';
        }

        return 'LIMIT ' . $this->limit;
    }
}