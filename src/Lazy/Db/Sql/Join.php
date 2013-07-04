<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Connection;


/**
 * Class Join
 * @package Lazy\Db\Sql
 */
class Join
{
    /**
     *
     */
    const INNER = 'INNER JOIN';
    /**
     *
     */
    const LEFT  = 'LEFT JOIN';
    /**
     *
     */
    const RIGHT = 'RIGHT JOIN';

    /**
     * @var array
     */
    protected $joins = array();

    /**
     * @param string $tableName optional
     * @param string $conditions optional
     */
    public function __construct($join = null)
    {
        if ($join) {
            call_user_func_array(array($this, 'join'), func_get_args());
        }
    }

    /**
     * @param string $type
     * @param string $tableName
     * @param string $conditions optional
     * @return $this
     */
    protected function _join($type, $tableName, $conditions = null)
    {
        if ($tableName instanceof self) {
            $this->joins[] = $tableName;
            return $this;
        }

        $this->joins[] = array(
            'type'          => $type,
            'tableName'     => $tableName,
            'conditions'    => $conditions
        );

        return $this;
    }

    /**
     * @param string $tableName optional
     * @param string $conditions optional
     * @return $this|array
     */
    public function join($tableName = null, $conditions = null)
    {
        if (!$tableName) {
            return $this->joins;
        }

        return $this->_join(self::INNER, $tableName, $conditions);
    }

    /**
     * @param string $tableName
     * @param string $conditions optional
     * @return $this
     */
    public function leftJoin($tableName, $conditions = null)
    {
        return $this->_join(self::LEFT, $tableName, $conditions);
    }

    /**
     * @param string $tableName
     * @param string $conditions optional
     * @return $this
     */
    public function rightJoin($tableName, $conditions = null)
    {
        return $this->_join(self::RIGHT, $tableName, $conditions);
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->joins = array();
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->joins) {
            return '';
        }

        $parts = array();
        foreach ($this->joins as $join) {
            if ($join instanceof self) {
                $parts[] = $join->toString();
                continue;
            }

            $part = $join['type'] . ' ' . $join['tableName'];

            if ($join['conditions']) {
                $part .= ' ON ' . $join['conditions'];
            }
            $parts[] = $part;
        }

        return implode(' ', $parts);
    }
}