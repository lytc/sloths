<?php

namespace Lazy\Db\Sql;

use Lazy\Db\Connection;

/**
 * Class AbstractCondition
 * @package Lazy\Db\Sql
 */
abstract class AbstractCondition
{
    protected $connection;

    /**
     * @var string
     */
    protected $type;

    /**
     * @var array
     */
    protected $conditions = array();

    /**
     * @param Connection $connection
     */
    public function __construct(Connection $connection)
    {
        $this->connection = $connection;
    }

    /**
     * @param string $type
     * @param string|array $conditions
     * @param array $bindParams
     * @return $this
     */
    protected function condition($type, $conditions = null, array $bindParams = null)
    {

        $this->conditions[] = array(
            'type'          => $type,
            'conditions'    => $conditions,
            'bindParams'    => $bindParams
        );
        return $this;
    }

    /**
     * @param array $option
     * @return array
     */
    protected function compile(array $option)
    {
        $type = $option['type'];
        $conditions = $option['conditions'];
        $bindParams = $option['bindParams'];

        if (is_string($conditions)) {
            if ($bindParams) {
                $conditions = $this->bind($conditions, $bindParams);
            }
            return array(
                'type' => $type,
                'conditions' => $conditions
            );
        }

        $parts = array();
        $first = true;
        foreach ($conditions as $condition => $bindParams) {
            if (is_numeric($condition)) {
                $condition = $bindParams;
                $bindParams = null;
            } else {
                if (preg_match('/^\w+\.?\w+$/', $condition)) {
                    if (null === $bindParams) {
                        $condition = "$condition IS NULL";
                    } else {
                        $condition = "$condition = ?";
                    }
                }

            }

            if (null !== $bindParams) {
                $bindParams = (array) $bindParams;
            }

            if ($bindParams) {
                $condition = $this->bind($condition, $bindParams);
            }

            if (!$first && !preg_match('/^(or|OR|and|AND)\s+/', $condition)) {
                $condition = 'AND ' . $condition;
            }
            $parts[] = $condition;
            $first = false;
        }

        return array(
            'type'  => $type,
            'conditions' => implode(' ', $parts)
        );
    }

    /**
     * @param string $conditions
     * @param array $bindParams
     * @return string
     */
    protected function bind($conditions, array $bindParams) {
        foreach ($bindParams as &$value) {
            $value = $this->connection->quote($value);
        }

        $index = 0;
        return preg_replace_callback(array('/\(?(\?|\:(\w+))\)?([^\w]*)/', '/\(?(\?|\:(\w+))\)?$/'), function($matches) use(&$index, $bindParams) {
            if (preg_match('/^\((\?|\:(\w+))\)$/', $matches[0], $m)) {
                if($m[0] == '(?)') {
                    $value = $bindParams[$index++];
                    if (!is_array($value)) {
                        $value = $bindParams;
                    }
                } else {
                    $value = $bindParams[$m[2]];
                }

                if (is_array($value)) {
                    $value = implode(', ', $value);
                }

                return '(' . $value . ')';
            }

            if ($matches[1] == '?') {
                return $bindParams[$index++] . (isset($matches[3])? $matches[3] : '');
            }

            return $bindParams[$matches[2]] . (isset($matches[3])? $matches[3] : '');
        }, $conditions);
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->conditions = array();
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        if (!$this->conditions) {
            return '';
        }

        $parts = array();

        foreach ($this->conditions as $condition) {
            $parts[] = $this->compile($condition);
        }

        $part0 = array_shift($parts);
        $conditions = array('(' . $part0['conditions'] . ')');
        foreach ($parts as $part) {
            $conditions[] = sprintf('%s (%s)', $part['type'], $part['conditions']);
        }

        return sprintf('%s %s', $this->type, implode(' ', $conditions));
    }
}