<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\AbstractModel;

class Role extends AbstractModel
{
    protected static $columns = [
        'id' => self::INT,
        'name' => self::VARCHAR
    ];
}