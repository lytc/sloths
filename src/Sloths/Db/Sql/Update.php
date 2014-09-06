<?php

namespace Sloths\Db\Sql;

/**
 * @method Update values(array $values)
 * @method Update where($condition, $params = null)
 * @method Update orWhere($condition, $params = null)
 * @method Update orderBy($columns)
 * @method Update limit(int $limit)
 */
class Update extends AbstractSql implements SqlWriteInterface
{
    const SPEC_SET      = 'Set';
    const SPEC_WHERE    = 'Where';
    const SPEC_ORDER_BY = 'OrderBy';
    const SPEC_LIMIT    = 'Limit';
    /**
     * @var array
     */
    protected $specs = [
        self::SPEC_SET      => null,
        self::SPEC_WHERE    => null,
        self::SPEC_ORDER_BY => null,
        self::SPEC_LIMIT    => null
    ];

    /**
     * @var array
     */
    protected $methods = [
        'values'    => [self::SPEC_SET, 'values'],
        'where'     => [self::SPEC_WHERE, 'and'],
        'orWhere'   => [self::SPEC_WHERE, 'or'],
        'orderBy'   => [self::SPEC_ORDER_BY, 'add'],
        'limit'     => [self::SPEC_LIMIT, 'limit'],
    ];

    /**
     * @var string
     */
    protected $tableName;

    /**
     * @param string $tableName
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
        return 'UPDATE ' . $this->tableName . ' ' . parent::toString();
    }
}