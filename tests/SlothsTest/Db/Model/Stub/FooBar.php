<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\Model;

class FooBar extends Model
{
    protected static $primaryKey = 'foo_id';
    protected static $tableName = 'foo_bar';
}