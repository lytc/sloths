<?php

namespace Sloths\Db\Sql;

/**
 * @method Select table(string $tableName)
 * @method Select select($columns)
 * @method Select join($table, $condition)
 * @method Select innerJoin($table, $condition)
 * @method Select leftJoin($table, $condition)
 * @method Select rightJoin($table, $condition)
 * @method Select where($condition, $params = null)
 * @method Select orWhere($condition, $params = null)
 * @method Select having($condition, $params = null)
 * @method Select orHaving($condition, $params = null)
 * @method Select groupBy($columns)
 * @method Select orderBy($columns)
 * @method Select limit(int $limit)
 * @method Select offset(int $offset)
 */
class Select extends AbstractSql implements SqlReadInterface
{
    const SPEC_SELECT   = 'Select';
    const SPEC_JOIN     = 'Join';
    const SPEC_WHERE    = 'Where';
    const SPEC_GROUP_BY = 'GroupBy';
    const SPEC_HAVING   = 'Having';
    const SPEC_ORDER_BY = 'OrderBy';
    const SPEC_LIMIT    = 'Limit';

    /**
     * @var array
     */
    protected $specs = [
        self::SPEC_SELECT   => null,
        self::SPEC_JOIN     => null,
        self::SPEC_WHERE    => null,
        self::SPEC_GROUP_BY => null,
        self::SPEC_HAVING   => null,
        self::SPEC_ORDER_BY => null,
        self::SPEC_LIMIT    => null
    ];

    /**
     * @var array
     */
    protected $methods = [
        'table'     => [self::SPEC_SELECT, 'setTableName'],
        'select'    => [self::SPEC_SELECT, 'addColumns'],
        'join'      => [self::SPEC_JOIN, 'inner'],
        'innerJoin' => [self::SPEC_JOIN, 'inner'],
        'leftJoin'  => [self::SPEC_JOIN, 'left'],
        'rightJoin' => [self::SPEC_JOIN, 'right'],
        'where'     => [self::SPEC_WHERE, 'and'],
        'orWhere'   => [self::SPEC_WHERE, 'or'],
        'groupBy'   => [self::SPEC_GROUP_BY, 'add'],
        'having'    => [self::SPEC_HAVING, 'and'],
        'orHaving'  => [self::SPEC_HAVING, 'or'],
        'orderBy'   => [self::SPEC_ORDER_BY, 'add'],
        'limit'     => [self::SPEC_LIMIT, 'limit'],
        'offset'    => [self::SPEC_LIMIT, 'offset'],
    ];
}