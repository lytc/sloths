<?php

namespace Sloths\Db\Sql\Spec;

class Set extends Value
{
    public function toString()
    {
        return 'SET ' . parent::toString();
    }
}