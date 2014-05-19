<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\Model;

class Role extends Model
{
    protected static $columns = [
        'id' => self::INT,
        'name' => self::VARCHAR,
        'description' => self::TEXT
    ];
}