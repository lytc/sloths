<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\AbstractModel;

class User extends AbstractModel
{
    protected static $columns = [
        'id' => self::INT,
        'username' => self::VARCHAR,
        'password' => self::VARCHAR,
        'created_time' => self::DATETIME,
        'modified_time' => self::DATETIME
    ];

    protected static $hasOne = [
        'Profile'
    ];

    protected static $belongsTo = [
        'Group'
    ];

    protected static $hasMany = [
        'Posts',
        'Roles' => ['through' => 'UserRole']
    ];
}