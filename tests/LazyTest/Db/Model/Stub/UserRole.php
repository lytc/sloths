<?php

namespace LazyTest\Db\Model\Stub;

use Lazy\Db\Model\Model;

class UserRole extends Model
{
    protected static $columns = [
        'id' => self::INT,
        'user_id' => self::INT,
        'role_id' => self::INT
    ];
}