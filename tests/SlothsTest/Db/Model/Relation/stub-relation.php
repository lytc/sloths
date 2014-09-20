<?php

namespace MockModel;

use Sloths\Db\Model\AbstractModel;

class User extends AbstractModel
{
    protected $columns = [
        'id' => self::INT
    ];

    protected $hasOne = [
        'Profile'
    ];

    protected $hasMany = [
        'Posts',
        'Roles' => ['through' => 'UserRole']
    ];
}

class Post extends AbstractModel
{
    protected $columns = [
        'id' => self::INT,
        'user_id' => self::INT,
        'title' => self::VARCHAR
    ];

    protected $belongsTo = [
        'User'
    ];
}

class Role extends AbstractModel
{
    protected $columns = [
        'id' => self::INT,
        'name' => self::VARCHAR
    ];

    protected $hasMany = [
        'Users' => ['through' => 'UserRole']
    ];
}

class UserRole extends AbstractModel
{
    protected $belongsTo = [
        'User',
        'Role'
    ];
}

class Profile extends AbstractModel
{
    protected $columns = [
        'user_id' => self::INT,
        'resume' => self::TEXT
    ];

    protected $belongsTo = [
        'User'
    ];
}