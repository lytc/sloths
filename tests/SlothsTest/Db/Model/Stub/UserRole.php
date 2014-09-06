<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\AbstractModel;

class UserRole extends AbstractModel
{
    protected static $columns = [
        'id'        => self::INT,
        'user_id'   => self::INT,
        'role_id'   => self::INT,
    ];

    protected static $belongsTo = [
        'Role',
        'User'
    ];
}