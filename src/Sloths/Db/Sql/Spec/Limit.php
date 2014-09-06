<?php

namespace Sloths\Db\Sql\Spec;

use Sloths\Db\Sql\SqlInterface;

class Limit implements SqlInterface
{
    /**
     * @var int
     */
    protected $limit;
    /**
     * @var int
     */
    protected $offset;

    /**
     * @param int $limit
     * @param int $offset
     * @return $this
     */
    public function limit($limit, $offset = null)
    {
        $this->limit = $limit;

        if (is_numeric($offset)) {
            $this->offset = $offset;
        }

        return $this;
    }

    /**
     * @param int $offset
     * @return $this
     */
    public function offset($offset)
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->limit = null;
        $this->offset = null;

        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!is_numeric($this->limit)) {
            return '';
        }

        $result = 'LIMIT ' . $this->limit;

        if ($this->offset) {
            $result .= ' OFFSET ' . $this->offset;
        }

        return $result;
    }
}