<?php

namespace LazyTest\Db\Model\Stub;

use Lazy\Db\Model\Model;

class FooBar extends Model
{
    protected static $primaryKey = 'foo_id';
    protected static $tableName = 'foo_bar';
}