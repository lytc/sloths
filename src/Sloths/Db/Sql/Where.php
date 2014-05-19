<?php

namespace Sloths\Db\Sql;

class Where extends AbstractWhereHaving
{
    protected $prefix = 'WHERE';

    public function where($conditions)
    {
        return call_user_func_array([$this, 'addAndCondition'], func_get_args());
    }

    public function orWhere($conditions)
    {
        return call_user_func_array([$this, 'addOrCondition'], func_get_args());
    }
}