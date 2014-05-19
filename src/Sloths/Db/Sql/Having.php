<?php

namespace Sloths\Db\Sql;

class Having extends AbstractWhereHaving
{
    protected $prefix = 'HAVING';

    public function having($conditions)
    {
        return call_user_func_array([$this, 'addAndCondition'], func_get_args());
    }

    public function orHaving($conditions)
    {
        return call_user_func_array([$this, 'addOrCondition'], func_get_args());
    }
}