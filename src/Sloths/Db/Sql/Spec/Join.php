<?php

namespace Sloths\Db\Sql\Spec;

use Sloths\Db\Sql\SqlInterface;

class Join implements SqlInterface
{
    const INNER_JOIN    = 'INNER JOIN';
    const LEFT_JOIN     = 'LEFT JOIN';
    const RIGHT_JOIN    = 'RIGHT JOIN';

    /**
     * @var array
     */
    protected $joins = [];

    /**
     * @param string $type
     * @param string $tableName
     * @param string|array|callable $condition
     * @return $this
     */
    protected function add($type, $tableName, $condition)
    {
        $filter = new On();
        $filter->add($condition);

        $this->joins[] = [
            'type'      => $type,
            'tableName' => $tableName,
            'filter'    => $filter
        ];
        return $this;
    }

    /**
     * @param string $tableName
     * @param string|array|callable $condition
     * @return $this
     */
    public function inner($tableName, $condition)
    {
        return $this->add(self::INNER_JOIN, $tableName, $condition);
    }

    /**
     * @param string $tableName
     * @param string|array|callable $condition
     * @return mixed
     */
    public function left($tableName, $condition)
    {
        return $this->add(self::LEFT_JOIN, $tableName, $condition);
    }

    /**
     * @param string $tableName
     * @param string|array|callable $condition
     * @return mixed
     */
    public function right($tableName, $condition)
    {
        return $this->add(self::RIGHT_JOIN, $tableName, $condition);
    }

    /**
     * @return $this
     */
    public function reset()
    {
        $this->joins = [];
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

        $result = [];
        foreach ($this->joins as $join) {
            $result[] = $join['type'] . ' ' . $join['tableName'] . ' ' . $join['filter']->toString();
        }

        return implode(' ', $result);
    }
}