<?php

namespace Sloths\Db\Sql;

/**
 * @method Insert values(array $values)
 */
class Insert extends AbstractSql implements SqlWriteInterface
{
    const SPEC_SET = 'Set';
    /**
     * @var array
     */
    protected $specs = [
        self::SPEC_SET => null,
    ];

    /**
     * @var array
     */
    protected $methods = [
        'values' => [self::SPEC_SET, 'values']
    ];

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param $tableName
     * @return $this
     */
    public function table($tableName)
    {
        $this->tableName = $tableName;
        return $this;
    }

    /**
     * @return string
     */
    public function toString()
    {
        $result = 'INSERT INTO ' . $this->tableName;
        $result .= ' ' . parent::toString();

        return $result;
    }
}