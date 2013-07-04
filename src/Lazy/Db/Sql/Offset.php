<?php

namespace Lazy\Db\Sql;

/**
 * Class Offset
 * @package Lazy\Db\Sql
 */
class Offset
{
    /**
     * @var int
     */
    protected $offset;

    /**
     * @param int $offset optional
     */
    public function __construct($offset = null)
    {
        if ($offset) {
            $this->offset($offset);
        }
    }

    /**
     * @param int $offset optional
     * @return $this
     */
    public function offset($offset = null)
    {
        if (!$offset) {
            return $this->offset;
        }

        $this->offset = (int) $offset;

        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->offset = null;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->offset) {
            return '';
        }

        return 'OFFSET ' . $this->offset;
    }
}