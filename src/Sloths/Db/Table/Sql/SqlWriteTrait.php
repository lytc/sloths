<?php

namespace Sloths\Db\Table\Sql;

trait SqlWriteTrait
{
    public function run()
    {
        return $this->getConnection()->exec($this->toString());
    }
}