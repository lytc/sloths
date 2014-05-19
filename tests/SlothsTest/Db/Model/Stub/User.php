<?php

namespace SlothsTest\Db\Model\Stub;

use Sloths\Db\Model\Model;

class User extends Model
{
    protected static $columns = [
        'id' => self::INT,
        'name' => self::VARCHAR,
        'password' => self::VARCHAR,
        'profile' => self::TEXT,
        'created_time' => self::DATETIME
    ];

    protected static $hiddenColumns = ['password'];

    protected static $hasMany = [
        'Posts' => [
            'model' => 'Post',
            'foreignKey' => 'created_user_id'
        ]
    ];

    protected static $hasOne = [
        'Professor'
    ];

    protected static $hasManyThrough = [
        'Roles' => [
            'model' => 'Role',
            'leftPrimaryKey' => 'id',
            'leftForeignKey' => 'user_id',
            'rightPrimaryKey' => 'id',
            'rightForeignKey' => 'role_id',
            'throughModel' => 'UserRole'
        ]
    ];
}