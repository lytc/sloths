<?php

namespace LazyTest\Db\Model\Stub;

use Lazy\Db\Model\Model;

class Role extends Model
{
    protected static $columns = [
        'id' => self::INT,
        'name' => self::VARCHAR,
        'description' => self::TEXT
    ];
}