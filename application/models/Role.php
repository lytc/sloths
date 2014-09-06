<?php

namespace Application\Model;

use Sloths\Db\Model\AbstractModel;

class Role extends AbstractModel
{
    protected static $columns = [
        'id'                => self::INT,
        'name'              => self::VARCHAR,
        'created_time'      => self::DATETIME,
        'modified_time'     => self::DATETIME,
    ];

    protected static $hasMany = [
        'Users' => [
            'model'             => 'Application\Model\User',
            'through'           => 'Application\Model\RoleUser',
//            'leftForeignKey'    => 'role_id',
//            'rightForeignKey'   => 'user_id',
        ],
    ];
}