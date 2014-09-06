<?php

namespace Sloths\Db\Sql;

/**
 * @method Delete where($condition, $params = null)
 * @method Delete orWhere($condition, $params = null)
 * @method Delete orderBy($columns)
 * @method Delete limit(int $limit)
 */
class Delete extends AbstractSql implements SqlWriteInterface
{
    const SPEC_WHERE = 'Where';
    const SPEC_ORDER_BY = 'OrderBy';
    const SPEC_LIMIT = 'Limit';

    /**
     * @var array
     */
    protected $specs = [
        self::SPEC_WHERE    => null,
        self::SPEC_ORDER_BY => null,
        self::SPEC_LIMIT    => null
    ];

    /**
     * @var array
     */
    protected $methods = [
        'where'     => [self::SPEC_WHERE, 'and'],
        'orWhere'   => [self::SPEC_WHERE, 'or'],
        'orderBy'   => [self::SPEC_ORDER_BY, 'add'],
        'limit'     => [self::SPEC_LIMIT, 'limit'],
    ];

    /**
     * @var
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
        $sql = parent::toString();
        return 'DELETE FROM ' . $this->tableName . ($sql? ' ' . $sql : '');
    }
}