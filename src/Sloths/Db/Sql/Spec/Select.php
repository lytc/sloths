<?php

namespace Sloths\Db\Sql\Spec;

use Sloths\Db\Sql\SqlInterface;

class Select implements SqlInterface
{
    /**
     * @var string
     */
    protected $tableName;

    /**
     * @var array
     */
    protected $columns = [];

    public function __construct($tableName = null, $columns = null)
    {
        if ($tableName) {
            $this->setTableName($tableName);
        }

        if ($columns) {
            $this->addColumns($columns);
        }
    }

    /**
     * @param $tableName
     * @return $this
     */
    public function setTableName($tableName)
    {
        $this->tableName = trim($tableName);
        return $this;
    }

    /**
     * @param string|array $columns
     * @return $this
     */
    public function addColumns($columns)
    {
        if (!is_array($columns)) {
            $columns = [$columns];
        }

        $this->columns = array_merge($this->columns, $columns);
        return $this;
    }

    /**
     * @return $this
     */
    public function resetColumns()
    {
        $this->columns = [];
        return $this;
    }

    /**
     *
     */
    public function toString()
    {
        $parts = ['SELECT'];

        list($tableName, $tableAliasName) = array_replace([null, null], preg_split('/(\s+as\s+|\s+)/i', $this->tableName, 2));

        # columns
        $columnPrefix = $tableAliasName?: $tableName;

        $columns = [];
        foreach ($this->columns?: ['*'] as $columnAliasName => $columnName) {
            if ('*' == $columnName) {
                $columns[] = $columnPrefix . '.*';
            } else {
                $column = $columnName . (!is_numeric($columnAliasName)? ' AS ' . $columnAliasName : '');

                if (!$columnName instanceof Raw) {
                    if (preg_match('/^\w+$/', $columnName)) {
                        $column = $columnPrefix . '.' . $column;
                    } elseif ('#' == $column[0]) {
                        $column = substr($column, 1);
                    }
                }

                $columns[] = $column;
            }
        }

        $parts[] = implode(', ', $columns);

        # from
        $fromPart = 'FROM ' . $tableName;

        if ($tableAliasName && $tableAliasName != $tableName) {
            $fromPart .= ' AS ' . $tableAliasName;
        }

        $parts[] = $fromPart;

        return implode(' ', $parts);
    }
}