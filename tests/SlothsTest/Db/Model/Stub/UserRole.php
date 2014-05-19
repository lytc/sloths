<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\Model;

class UserRole extends Model
{
    protected static $columns = [
        'id' => self::INT,
        'user_id' => self::INT,
        'role_id' => self::INT
    ];
}